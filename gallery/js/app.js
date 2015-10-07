$(function () {
    var canvas = window._canvas = new fabric.Canvas('appCanvas');
    var pattern;

    fabric.Object.prototype.set({
        transparentCorners: false,
        cornerColor: 'rgba(0,0,0,0.5)',
        cornerSize: 10,
        padding: 0,
        stroke: 2
    });

    var mode = "add",
        currentShape,
        lastShape;

    canvas.observe("mouse:move", function (event) {
        var pos = canvas.getPointer(event.e);
        if (mode === "edit" && currentShape) {
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

        if (mode === "add") {
            var polygon = new fabric.Polygon([{
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
            mode = "edit";
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
            if (mode === 'edit' || mode === 'add') {
                mode = 'normal';
                currentShape.set({
                    selectable: true
                });
                currentShape._calcDimensions(false);
                currentShape.setCoords();
            } else {
                mode = 'add';
            }
            lastShape = currentShape;
            currentShape = null;
        }

        if (e.keyCode === 46 || e.keyCode === 8) {
            canvas.item(0).remove();
            currentShape = null;
            mode = 'add';
        }

    });

    function loadTexture(url) {
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

        setTextureToLastShape();
    }

    function setTextureToLastShape(){
        lastShape.set({
            fill: pattern
        });
        canvas.renderAll();
    }

    $('.images img').click(function () {
        $('.images img').css('border', 0);
        loadTexture($(this).data('url'))
        $(this).css('border', '1px solid red');
    });
});