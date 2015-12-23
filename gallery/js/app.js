var App = {};
App = (function ($, app) {
    var self;
    return $.extend(app, {
        init: function () {
            self = this;
            $(function () {
                self.executeReady();
            });
        },
        isReady: false,
        readyFn: [],
        ready: function (fn) {
            self.readyFn.push(fn);
        },
        executeReady: function () {
            self.isReady = true;
            var fns = self.readyFn,
                i = 0,
                max = fns.length;
            for (; i < max; i++) {
                var fn = fns[i];
                if (typeof fn === "function") {
                    fn();
                }
            }
            self.readyFn.length = 0;
        }
    });
})(jQuery, App);
App.init();
App.Main = (function ($, app, fabric, slick) {
    var self,
        canvas,
        currentShape,
        polygon,
        config,
        lastId = 0;
    return {
        init: function (c) {
            config = c;
            self = this;
            app.ready(function () {
                self.onDownload();
                self.initCanvas();
                self.initFabric();
                self.background.listen();
                self.fulfillment.onSelectImage();
                self.restart();
            });
        },
        initCanvas: function () {
            canvas = new fabric.Canvas('appCanvas');
            canvas.defaultCursor = 'crosshair';
        },
        initFabric: function () {
            fabric.Object.prototype.set({
                stroke: 1
            });
            canvas.observe("mouse:down", function (event) {
                var pos = canvas.getPointer(event.e);
                if (self.mode.isCurrent('create')) {
                    currentShape = new fabric.Polygon([{
                        x: pos.x,
                        y: pos.y
                    }, {
                        x: pos.x + 0.5,
                        y: pos.y + 0.5
                    }], {
                        opacity: 1,
                        borderColor: 'ccc',
                        originX: pos.x,
                        originY: pos.y,
                        id: ++lastId
                    });
                    canvas.add(currentShape);
                    self.mode.set('edit');
                }
                else if (self.mode.isCurrent('edit') && currentShape && currentShape.type === "polygon") {
                    var points = currentShape.get("points");
                    points.push({
                        x: pos.x - currentShape.get("left"),
                        y: pos.y - currentShape.get("top")
                    });
                    currentShape.set({
                        points: points
                    });
                    canvas.renderAll();
                }
            });

            canvas.observe("object:selected", function (e) {
                if (self.mode.isCurrent('normal')) {
                    currentShape = e.target;
                    self.fulfillment.reInitSliders();
                }
            });

            canvas.observe("object:scaling", function (e) {
                if (currentShape.image !== undefined) {
                    //TODO: fix bg ratio here
                }
            });

            canvas.observe("selection:cleared", function () {
                if (self.mode.isCurrent('normal')) {
                    self.fulfillment.resetSliders();
                    currentShape = null;
                }
            });

            canvas.observe("selection:removed", function () {
                self.fulfillment.resetSliders();
                currentShape = null;
            });

            fabric.util.addListener(window, 'keyup', function (e) {
                //del || backspace				
                if (e.keyCode === 46 || e.keyCode === 8) {
                    if (currentShape) {
                        currentShape.remove();
                        currentShape = null;
                    }
                }
            });
        },
        setupDrawArea: function () {
            $('#intro').hide();
            $('#main').show();
            $('#navigation').show();
            $('#gallery').show();
            self.gallery.render();
            self.mode.set('draw');
        },
        setupIntro: function () {
            $('#navigation').hide();
            $('#main').hide();
            $('#gallery').hide();
            $('#intro').slideDown('fast');
        },
        onDownload: function () {
            $('img').mousedown(function (e) {
                if (e.button == '2') {
                    return false;
                }
            });
            $('.saveas').click(function () {
                var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                $('.downloadJpg').attr({
                    'download': 'szklane-zabudowy.png',
                    'href': image
                });
            });
        },
        restart: function () {
            $('.reset').click(function () {
                canvas.clear();
                self.setupIntro();
            });
        },
        mode: (function () {
            var that;
            var current;
            return {
                init: function () {
                    that = this;
                    $('.draw-mode').click(function () {
                        that.set('create');
                    });
                    $('.normal-mode').click(function () {
                        that.set('normal');
                    });
                    $('.remove-button').click(function () {
                        if (!currentShape) {
                            return;
                        }
                        currentShape.remove();
                        currentShape = null;
                        if (!canvas.item(0)) {
                            $(this).addClass('hidden');
                        }
                    });

                    return that;
                },
                set: function (mode) {
                    switch (mode) {
                        case 'normal':
                            that.setNormalMode();
                            break;
                        case 'edit':
                            that.setEditMode();
                            break;
                        case 'create':
                            that.setCreateMode();
                            break;
                    }
                },
                isCurrent: function (mode) {
                    return ( current === mode);
                },
                setNormalMode: function () {
                    if (current == 'normal') {
                        return;
                    }
                    current = 'normal';
                    $('.dropdown-toggle').prop('disabled', true);
                    $('#appCanvas').css('cursor', 'pointer');
                    $('.draw-mode').attr('disabled', false);
                    $('.normal-mode').attr('disabled', true);
                    $('.remove-button').removeClass('hidden');
                    $('#info').fadeOut('fast').html('Wybierz teksturę wypełnienia')
                        .fadeIn('fast');
                    $('#backgroundTools').removeClass('hidden');
                    currentShape.set({
                        selectable: true
                    });

                    currentShape.left = currentShape.originX;
                    currentShape.top = currentShape.originY;
                    currentShape.originX = "left";
                    currentShape.originY = "top";
                    currentShape.selected = true;
                    var id = currentShape.id;
                    canvas.hoverCursor = 'pointer';
                    canvas.selection = false; //disable group selection

                    canvas.renderAll();
                    var json = JSON.stringify(canvas);
                    canvas.loadFromJSON(json, function () {
                        canvas.renderAll();
                    });

                    canvas.setActiveObject(canvas.item(id - 1));

                },
                setEditMode: function () {
                    $('#appCanvas').css('cursor', 'crosshair');
                    current = 'edit';
                    $('.draw-mode').attr('disabled', true);
                    $('.normal-mode').attr('disabled', false);
                    $('#info')
                        .fadeOut('fast')
                        .html('Gdy zakończysz rysować kształt do wypełnienia wciśnij klawisz <key>ESC</key>')
                        .fadeIn('fast');
                    $('.remove-button').addClass('hidden');
                    $('#backgroundTools').addClass('hidden');
                },
                setCreateMode: function () {
                    $('#appCanvas').css('cursor', 'crosshair');
                    current = 'create';
                    $('.draw-mode').attr('disabled', true);
                    $('.normal-mode').attr('disabled', false);
                    $('#info')
                        .fadeOut('fast')
                        .html('Narysuj kształt do wypełnienia')
                        .fadeIn('fast');
                    $('.remove-button').addClass('hidden');
                    $('#backgroundTools').addClass('hidden');
                }
            };
        })().init(),
        gallery: (function (slick) {
            var that;
            var $galleryContainer = $('#gallery');
            var slickConfig = {
                arrows: true,
                infinite: true,
                speed: 200,
                slidesToScroll: 4,
                variableWidth: true
            };
            return {
                init: function () {
                    return that = this;
                },
                reInitSlick: function () {
                    $('.images-container').slick('reinit');
                },
                render: function () {
                    $.ajax({
                        method: 'get',
                        url: config.galleryDataUrl,
                        dataType: 'json',
                        success: function (gallery) {
                            var $tabsContainer = $('<ul class="nav nav-tabs"></ul>');
                            var $albumsContainer = $('<div class="tab-content"></div>');
                            $galleryContainer.append($tabsContainer);
                            $galleryContainer.append($albumsContainer);
                            var i = 0;
                            $.each(gallery, function (id, album) {
                                $tabsContainer.append(that.createTabElement(album, i === 0));
                                $albumsContainer.append(that.createAlbumElement(album, i === 0));
                                i++;
                            });
                            that.reInitSlick(); //reinit on load first tab
                            $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
                                that.reInitSlick(); //reinit on tab change
                            });
                        }
                    })
                },
                createTabElement: function (album, active) {
                    var albumId = 'album_' + album['AlbumID'];
                    return $('<li ' + (active ? 'class="active"' : '') + '"><a href="#' + albumId + '" data-toggle="tab">' + album['AlbumName'] + '</a></li>');
                },
                createAlbumElement: function (album, active) {
                    var albumId = 'album_' + album['AlbumID'];
                    var $imagesContainer = $('<div class="tab-pane images-container' + (active ? ' active' : '') + '" id="' + albumId + '" />');
                    $imagesContainer.slick(slickConfig);
                    $.each(album.images, function (id, image) {
                        $imagesContainer.slick('slickAdd', '<div class="slick-frame"><img class="gallery-image" src="' + image['DefaultThumbnail'] + '" data-url="' + image['ImagePath'] + '" /></div>');
                    });

                    return $imagesContainer;
                }
            };
        })(slick).init(),
        fulfillment: (function () {
            var that;
            return {
                init: function () {
                    that = this;
                    that.initSliders();
                    return that;
                },
                initSliders: function () {
                    $('#imgWidth').on('change', function () {
                        if (currentShape && currentShape.image) {
                            currentShape.image.scaleToWidth(parseInt(this.value, 10));
                            canvas.renderAll();
                        }
                    });
                    $('#imgOffsetX').on('change', function () {
                        if (currentShape && currentShape.fill) {
                            currentShape.fill.offsetX = parseInt(this.value, 10);
                            canvas.renderAll();
                        }
                    });
                    $('#imgOffsetY').on('change', function () {
                        if (currentShape && currentShape.fill) {
                            currentShape.fill.offsetY = parseInt(this.value, 10);
                            canvas.renderAll();
                        }
                    });
                    $('.imgRepeat').on('change', function () {
                        currentShape.fill.repeat = this.value;
                        canvas.renderAll();
                        return false;
                    });
                },
                reInitSliders: function () {
                    if (currentShape.fill) {
                        var width = $('#imgWidth');
                        width.val(width.data('init-value'));
                        var offsetX = $('#imgOffsetX');
                        offsetX.val(currentShape.fill.offsetX);
                        var offsetY = $('#imgOffsetY');
                        offsetY.val(currentShape.fill.offsetY);
                        $('.imgRepeat').attr('checked', false);
                        $('.imgRepeat[value="' + currentShape.fill.repeat + '"]').attr('checked', true);
                    }
                },
                resetSliders: function () {
                    var width = $('#imgWidth');
                    width.val(width.data('init-value'));
                    var offsetX = $('#imgOffsetX');
                    offsetX.val(offsetX.data('init-value'));
                    var offsetY = $('#imgOffsetY');
                    offsetY.val(offsetY.data('init-value'));
                    $('.imgRepeat').attr('checked', false);
                    $('#imgRepeat').attr('checked', true);
                },
                enableSaveAs: function () {
                    var saveAs = $('.saveas');
                    saveAs.prop('disabled', false);
                    saveAs.fadeIn('fast');
                },
                onSelectImage: function () {
                    $('img.gallery-image').on('click', function () {
                        that.fill($(this));
                    });
                },
                fill: function (clickedImage) {
                    $('.draw-mode').hide();
                    $('.remove-button').hide();
                    var imageUrl = clickedImage.attr('data-url');
                    if (currentShape) {
                        fabric.Image.fromURL(imageUrl, function (img) {
                            if (img.width === 0 || img.height === 0) {
                                $('.modal-body').text('Nie udało się załadować zdjęcia');
                                $('.modal').modal('show');
                                return;
                            }
                            $('.gallery-image').removeClass('active');
                            clickedImage.addClass('active');
                            img.scaleToWidth(currentShape.getWidth());
                            img.set({strokeWidth: 0});
                            var patternSourceCanvas = new fabric.StaticCanvas();
                            patternSourceCanvas.add(img);
                            var pattern = new fabric.Pattern({
                                source: function () {
                                    if (currentShape) {
                                        //patternSourceCanvas.setDimensions({
                                        //    width: currentShape.getWidth(),
                                        //    height: currentShape.getHeight()
                                        //});
                                    }
                                    return patternSourceCanvas.getElement();
                                },
                                repeat: 'no-repeat'
                            });
                            that.enableSaveAs();
                            that.resetSliders();
                            currentShape.set({
                                fill: pattern,
                                image: img
                            });
                            canvas.renderAll();
                        });
                    } else {
                        $('.modal-body').text('Najpierw narysuj kształt wypełnienia');
                        $('.modal').modal('show');
                    }
                }
            };
        })().init(),
        background: (function () {
            var that;
            return {
                init: function () {
                    return that = this;
                },
                listen: function () {
                    $('#backgroundFileInput').change(function (e) {
                        self.setupDrawArea();
                        self.mode.set('create');
                        that.load(e);
                    });

                    $('.loadBackground').click(function () {
                        self.setupDrawArea();
                        self.mode.set('create');
                        var backgroundImage = $(this).data('image');
                        that.set(backgroundImage);
                    });
                },
                load: function (e) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var img = new Image();
                        img.onload = function () {
                            that.set(event.target.result);
                        };
                        img.src = event.target.result;
                    };
                    return reader.readAsDataURL(e.target.files[0]);
                },
                set: function (img) {
                    img.width = canvas.width;
                    img.height = canvas.height;
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                        width: canvas.width - 1,
                        height: canvas.height - 1,
                        originX: 'left',
                        originY: 'top'
                    });
                }
            }
        })().init()
    }
})(jQuery, App, fabric);