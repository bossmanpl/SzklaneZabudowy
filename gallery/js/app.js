var canvas = window._canvas = new fabric.Canvas('appCanvas');

fabric.Object.prototype.set({
    transparentCorners: false,
    cornerColor: 'rgba(0,0,0,0.2)',
    cornerSize: 10,
    padding: 0
});

var mode = "add",
    currentShape;

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
            fill: 'black',
            opacity: 0.2,
            selectable: false,
            borderColor: 'red',
            centeredRotation: true,
            hasControls: true
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
        currentShape = null;
    }
})