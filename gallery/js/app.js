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
App.Main = (function ($, app, fabric) {
    var self,
        canvas,
        currentShape,
        lastShape,
        polygon,
        pattern,
        backgroundImage;
    return {
        init: function () {
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
                console.log(currentShape);
            });

            canvas.observe("selection:cleared", function (e) {
                if (!self.mode.isCurrent('edit') && !self.mode.isCurrent('edit')) {
                    currentShape = null;
                }
            });

            canvas.observe("selection:removed", function (e) {
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
                    } else {
                        self.mode.set('create');
                    }
                    lastShape = currentShape;
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
                $(this).attr('href', canvas.toDataURL('image/jpeg'));
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
                    console.log(mode);
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
        gallery: (function () {
            var that;
            return {
                init: function () {
                    return that = this;
                },
                render: function () {
                    $.ajax({
                        method: 'get',
                        url: '/gallery.php',
                        dataType: 'json',
                        success: function (json) {
                            var gallery = JSON && JSON.parse(json) || $.parseJSON(json);
                            console.log(gallery);
                            //$('.images', '#gallery').html(gallery);
                        }
                    })
                }
            };
        })().init(),
        fulfillment: (function () {
            var that;
            return {
                init: function () {
                    return that = this;
                },
                onSelectImage: function () {
                    $('.images img').click(function () {
                        var image = $(this).attr('src');
                        if (currentShape) {
                            fabric.Image.fromURL(image, function (img) {
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
        })().init()
    }
})(jQuery, App, fabric);
App.Main.init();

