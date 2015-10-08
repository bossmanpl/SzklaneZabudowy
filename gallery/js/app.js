var App = App || {};
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
        },
        ajax: (function () {
            var ajax = function (options) {
                var standby;
                if (options && options.standby) {
                    standby = new self.Standby(options.standby);
                    $.extend(options, {
                        complete: function () {
                            standby.hide();
                        }
                    });
                }
                return $.ajax(options);
            };
            return {
                'get': function (options) {
                    $.extend(options || {}, {type: "GET"});
                    return ajax(options)
                },
                post: function () {
                    $.extend(options || {}, {type: "POST"});
                    return ajax(options)
                }
            }
        })()
    });
})(jQuery, App);
App.init();

App.Main = (function ($, app, fabric) {
    var self,
        canvas,
        currentShape,
        lastShape,
        mode,
        polygon,
        pattern;

    return {
        init: function () {
            self = this;
            app.ready(function () {
                self.onSelectImage();
                self.onReset();
                self.onDownload();
                self.onLoadBackground();
                self.onLoadDefaultBackground();
                self.setIntro();
            })
        },
        initCanvas: function () {
            canvas = window._canvas = new fabric.Canvas('appCanvas');
        },
        initFabric: function () {
            fabric.Object.prototype.set({
                transparentCorners: false,
                cornerColor: 'rgba(0,0,0,0.5)',
                cornerSize: 10,
                padding: 0,
                stroke: 2
            });
            canvas.observe("mouse:move", function (event) {
                var pos = canvas.getPointer(event.e);
                if (self.mode === "edit" && currentShape) {
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

                if (mode === "draw") {
                    polygon = new fabric.Polygon([{
                        x: pos.x,
                        y: pos.y
                    }, {
                        x: pos.x + 0.5,
                        y: pos.y + 0.5
                    }], {
                        opacity: 1,
                        selectable: false,
                        borderColor: 'red',
                        originX: 'center',
                        originY: 'center',
                        fill: pattern
                    });
                    currentShape = polygon;
                    canvas.add(currentShape);
                    self.enableEditMode()
                } else if (mode === "edit" && currentShape && currentShape.type === "polygon") {
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

            fabric.util.addListener(window, 'keyup', function (e) {
                if (e.keyCode === 27) {
                    if (mode === 'edit' || mode === 'draw' && currentShape) {
                        self.enableNormalMode();
                        currentShape.set({
                            selectable: true
                        });
                        currentShape._calcDimensions(false);
                        currentShape.setCoords();
                    } else {
                        self.enableDrawMode();
                    }
                    lastShape = currentShape;
                    currentShape = null;
                }
                if (e.keyCode === 46 || e.keyCode === 8) {
                    canvas.item(0).remove();
                    currentShape = null;
                    self.enableDrawMode();
                }
            });
        },
        enableNormalMode: function () {
            mode = 'normal';
            $('#info').fadeOut('fast').html('Wybierz teksturę wypełnienia')
                .fadeIn('fast');
        },
        enableEditMode: function () {
            mode = 'edit';
            $('#info')
                .fadeOut('fast')
                .html('Gdy zakończysz rysować kształt do wypełnienia wciśnij <key>ESC</key> (escape)')
                .fadeIn('fast');
        },
        enableDrawMode: function () {
            mode = 'draw';
            $('#info')
                .fadeOut('fast')
                .html('Narysuj kształt do wypełnienia')
                .fadeIn('fast');
        },
        loadTexture: function (url) {
            fabric.Image.fromURL(url, function (img) {
                var patternSourceCanvas = new fabric.StaticCanvas();
                patternSourceCanvas.add(img);
                pattern = new fabric.Pattern({
                    source: function () {
                        patternSourceCanvas.setDimensions({
                            width: img.getWidth(),
                            height: img.getHeight()
                        });
                        return patternSourceCanvas.getElement();
                    },
                    repeat: 'repeat'
                });
            });
        },
        setTextureToLastShape: function () {
            if (lastShape) {
                lastShape.set({
                    fill: pattern
                });
                canvas.renderAll();
            }
        },
        onSelectImage: function () {
            $('.images img').click(function () {
                $('.images img').css('border', 0);
                $(this).css('border', '1px solid red');
                self.loadTexture($(this).data('url'));
                self.setTextureToLastShape();
            });
        },
        onReset: function () {
            $('.reset').click(function () {
                canvas.clear();
                $('.images img').css('border', 0);
                self.setIntro();
            });
        },
        onDownload: function () {
            $('.downloadJpg').click(function () {
                $(this).attr('href', canvas.toDataURL('image/jpeg'));
            });
        },
        loadBackground: function (e) {
            var reader = new FileReader();
            reader.onload = function (event) {
                var img = new Image();
                img.onload = function () {
                    self.setBackground(img);
                }
                img.src = event.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        },
        onLoadBackground: function (e) {
            $('#backgroundFileInput').change(function () {
                self.start();
                self.setDrawArea();
                self.enableDrawMode();
                self.loadBackground(e);
            });
        },
        onLoadDefaultBackground: function () {
            $('#loadDefaultBackground').click(function () {
                self.start();
                self.setDrawArea();
                self.enableDrawMode();
                var img = new Image(canvas.width, canvas.height);
                img.src = 'images/sample.jpg';
                self.setBackground(img);
            });
        },
        setBackground: function (img) {
            img.width = canvas.width;
            img.height = canvas.height;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
        },
        start: function() {
            self.initCanvas();
            self.initFabric();
        },
        setDrawArea: function(){
            $('#intro').hide();
            $('#main').slideDown('fast');
            $('#navigation').show();
            $('#gallery').slideDown('fast');
            self.enableDrawMode();
        },
        setIntro: function(){
            $('#navigation').hide();
            $('#main').hide();
            $('#gallery').hide();
            $('#intro').slideDown('fast');
        }
    }
})(jQuery, App, fabric);
App.Main.init();

