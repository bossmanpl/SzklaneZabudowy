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
        backgroundImage,
        $buttons = $('.buttons', '#main');

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
            canvas.uniScaleTransform = true;
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
                if (self.mode.isCurrent('draw')) {
                    polygon = new fabric.Polygon([{
                        x: pos.x,
                        y: pos.y
                    }, {
                        x: pos.x + 0.5,
                        y: pos.y + 0.5
                    }], {
                        opacity: 1,
                        selectable: true,
                        borderColor: 'ccc',
                        fill: pattern
                    });
                    currentShape = polygon;
                    canvas.add(currentShape);
                    self.mode.set('edit');
                }
                else if (self.mode.isCurrent('edit') && currentShape && currentShape.type === "polygon") {
                    var points = currentShape.get("points");
                    points.push({
                        x: pos.x ,
                        y: pos.y
                    });
                    currentShape.set({
                        points: points
                    });
                    canvas.renderAll();
                }
            });

            fabric.util.addListener(window, 'keyup', function (e) {
                if (e.keyCode === 27) {
                    if (self.mode.isCurrent('edit') || self.mode.isCurrent('draw') && currentShape) {
                        self.mode.set('normal');
                        currentShape.set({
                            selectable: true
                        });
                        currentShape._calcDimensions(false);
                        currentShape.setCoords();
                    } else {
                        self.mode.set('draw');
                    }
                    lastShape = currentShape;
                    currentShape = null;
                }
                if (e.keyCode === 46 || e.keyCode === 8) {
                    canvas.item(0).remove();
                    currentShape = null;
                    self.mode.set('draw');
                }
            });
        },
        setupDrawArea: function () {
            $('#intro').hide();
            $('#main').slideDown('fast');
            $('#navigation').show();
            $('#gallery').slideDown('fast');
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
                    return that = this;
                },
                set: function (mode) {
                    switch (mode) {
                        case 'normal':
                            that.setNormalMode();
                            break;
                        case 'edit':
                            that.setEditMode();
                            break;
                        case 'draw':
                            that.setDrawMode();
                            break;
                    }
                    console.log(mode);
                },
                isCurrent: function (mode) {
                    return ( current === mode);
                },
                setNormalMode: function () {
                    current = 'normal';
                    $buttons.find('.btn').removeClass('disabled');
                    $buttons.find('.edit').addClass('disabled');
                    $('#info').fadeOut('fast').html('Wybierz teksturę wypełnienia')
                        .fadeIn('fast');
                }
                ,
                setEditMode: function () {
                    current = 'edit';
                    $buttons.find('.btn').removeClass('disabled');
                    $buttons.find('.normal').addClass('disabled');
                    $('#info')
                        .fadeOut('fast')
                        .html('Gdy zakończysz rysować kształt do wypełnienia wciśnij <key>ESC</key> (escape)')
                        .fadeIn('fast');
                },
                setDrawMode: function () {
                    current = 'draw';
                    $buttons.find('.btn').removeClass('disabled');
                    $buttons.find('.normal').addClass('disabled');
                    $('#info')
                        .fadeOut('fast')
                        .html('Narysuj kształt do wypełnienia')
                        .fadeIn('fast');
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
                        url: 'gallery.php',
                        dataType: 'json',
                        success: function (json) {
                            var gallery = JSON && JSON.parse(json) || $.parseJSON(json);
                            $('.images', '#gallery').html(gallery);
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
                loadTexture: function (url) {
                    fabric.Image.fromURL(url, function (img) {
                        var patternSourceCanvas = new fabric.StaticCanvas();
                        patternSourceCanvas.add(img);
                        pattern = new fabric.Pattern({
                            source: function () {
                                patternSourceCanvas.setDimensions({
                                    width: lastShape.width,
                                    height: lastShape.height
                                });
                                return patternSourceCanvas.getElement();
                            }
                        });
                    });
                },
                setTextureToLastShape: function () {
                    lastShape.set({
                        fill: pattern
                    });
                    canvas.renderAll();
                },
                onSelectImage: function () {
                    $('.images img').click(function () {
                        $('.images img').css('border', 0);
                        $(this).css('border', '1px solid red');
                        that.loadTexture($(this).data('url'));
                        that.setTextureToLastShape();
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
                        self.mode.set('draw');
                        that.load(e);
                    });

                    $('#loadDefaultBackground').click(function () {
                        self.setupDrawArea();
                        self.mode.set('draw');
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

