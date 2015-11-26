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
        pattern,
        backgroundImage,
        config;
    return {
        init: function (c) {
            config = c;
            self = this;
            app.ready(function () {
                self.onDownload();
                self.initCanvas();
                self.initFabric();
                self.background.init();
                self.fulfillment.onSelectImage();
                self.restart();
            });
        },
        initCanvas: function () {
            canvas = new fabric.Canvas('appCanvas');
        },
        initFabric: function () {
            fabric.Object.prototype.set({
                stroke: 2
            });
            canvas.observe("mouse:move", function (event) {
                var pos = canvas.getPointer(event.e);
                if (self.mode.isCurrent('edit') && currentShape) {
                    var points = currentShape.get("points");
                    points[points.length - 1].x = pos.x - currentShape.get("left");
                    points[points.length - 1].y = pos.y - currentShape.get("top");
                    currentShape.set({
                        points: points
                    });
                    canvas.renderAll();
                }
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
                        fill: pattern,
                        originX: pos.x,
                        originY: pos.y
                    });
                    canvas.add(currentShape);
                    self.mode.set('edit');
                }
                else if (self.mode.isCurrent('edit') && currentShape && currentShape.type === "polygon") {
                    var points = currentShape.get("points");
                    points.push({
                        x: pos.x,
                        y: pos.y
                    });
                    currentShape.set({
                        points: points
                    });
                    canvas.renderAll();
                }
            });

            canvas.observe("object:selected", function (e) {
                currentShape = e.target;
            });

            canvas.observe("selection:cleared", function () {
                if (!self.mode.isCurrent('edit') && !self.mode.isCurrent('edit')) {
                    currentShape = null;
                }
            });

            canvas.observe("selection:removed", function () {
                currentShape = null;
            });

            fabric.util.addListener(window, 'keyup', function (e) {
                if (e.keyCode === 27) {
                    if (self.mode.isCurrent('edit') || self.mode.isCurrent('create') && currentShape) {
                        self.mode.set('normal');
                        currentShape.set({
                            selectable: true
                        });
                        currentShape.left = currentShape.originX;
                        currentShape.top = currentShape.originY;
                        currentShape.originX = "left";
                        currentShape.originY = "top";
                        canvas.hoverCursor = 'pointer';
                    }
                    var json = JSON.stringify(canvas);
                    canvas.loadFromJSON(json, function () {
                        canvas.renderAll();
                    });
                    currentShape = null;
                }
                if (e.keyCode === 46 || e.keyCode === 8) {
                    currentShape.remove();
                    currentShape = null;
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
            $('.downloadJpg').click(function () {
                var dt = canvas.toDataURL('image/jpeg');
                dt = dt.replace(/^data:image\/[^;]*/, 'data:application/octet-stream');
                dt = dt.replace(/^data:application\/octet-stream/, 'data:application/octet-stream;headers=Content-Disposition%3A%20attachment%3B%20filename=Canvas.png');
                $(this).attr('href', dt);
            });
        },
        restart: function () {
            $('.reset').click(function () {
                canvas.clear();
                $('.images img').css('border', 0);
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
                        currentShape.remove();
                        currentShape = null;
                        that.set('create');
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
                    current = 'normal';
                    $('.draw-mode').attr('disabled', false);
                    $('.normal-mode').attr('disabled', true);
                    $('.remove-button').removeClass('hidden');
                    $('#info').fadeOut('fast').html('Wybierz teksturę wypełnienia')
                        .fadeIn('fast');
                },
                setEditMode: function () {
                    current = 'edit';
                    $('.draw-mode').attr('disabled', true);
                    $('.normal-mode').attr('disabled', false);
                    $('#info')
                        .fadeOut('fast')
                        .html('Gdy zakończysz rysować kształt do wypełnienia wciśnij <key>ESC</key> (escape)')
                        .fadeIn('fast');
                    $('.remove-button').addClass('hidden');
                },
                setCreateMode: function () {
                    current = 'create';
                    $('.draw-mode').attr('disabled', true);
                    $('.normal-mode').attr('disabled', false);
                    $('#info')
                        .fadeOut('fast')
                        .html('Narysuj kształt do wypełnienia')
                        .fadeIn('fast');
                    $('.remove-button').addClass('hidden');
                }
            };
        })().init(),
        gallery: (function (slick) {
            var that;
            var $galleryContainer = $('#gallery');
            var slickConfig = {
                slidesToShow: 6,
                slidesToScroll: 6,
                arrows: true
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
                    return that = this;
                },
                onSelectImage: function () {
                    $('body').on('click', 'img.gallery-image', function () {
                        var imageUrl = $(this).attr('data-url');
                        if (currentShape) {
                            fabric.Image.fromURL(imageUrl, function (img) {
                                if (img.width === 0 || img.height === 0) {
                                    alert('Nie udało się załadować zdjęcia.');
                                    return;
                                }
                                $('.gallery-image').removeClass('active');
                                $(this).addClass('active');
                                img.scaleToHeight(101);
                                var patternSourceCanvas = new fabric.StaticCanvas();
                                patternSourceCanvas.add(img);
                                var pattern = new fabric.Pattern({
                                    source: function () {
                                        patternSourceCanvas.setDimensions({
                                            width: img.getWidth(),
                                            height: img.getHeight()
                                        });
                                        return patternSourceCanvas.getElement();
                                    },
                                    repeat: 'repeat'
                                });
                                currentShape.set({
                                    fill: pattern
                                });
                                canvas.renderAll();
                            });
                        } else {
                            alert('Najpierw narysuj kształt wypełnienia.');
                        }
                    });
                }
            };
        })().init(),
        background: (function () {
            var that;
            return {
                init: function () {
                    var that = this;

                    $('#backgroundFileInput').change(function () {
                        self.setupDrawArea();
                        self.mode.set('create');
                        that.load(e);
                    });

                    $('#loadDefaultBackground').click(function () {
                        self.setupDrawArea();
                        self.mode.set('create');
                        backgroundImage = 'images/sample.jpg';
                        that.set(backgroundImage);
                    });

                    return that;
                },
                load: function (e) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var img = new Image();
                        img.onload = function () {
                            that.set(img);
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(e.target.files[0]);
                },
                set: function (img) {
                    img = img || backgroundImage;
                    img.width = canvas.width;
                    img.height = canvas.height;
                    canvas.setBackgroundImage(backgroundImage, canvas.renderAll.bind(canvas), {
                        width: canvas.width,
                        height: canvas.height,
                        originX: 'left',
                        originY: 'top'
                    });
                }
            }
        })()
    }
})(jQuery, App, fabric);
