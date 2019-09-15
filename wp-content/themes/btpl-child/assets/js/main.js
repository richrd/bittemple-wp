window.addEventListener('DOMContentLoaded', (event) => {
    const canvasSelector = '#fx-canvas';
    const root = document.querySelector('#root');

    const fx = new window.canvasFx.nodeFx({
        canvasSelector,
        // Node circle radius
        nodeRadius: 3,
        nodeLineWidth: 3,
        // Width of nodes and lines
        linkLineWidth: 3,
        // Strategy to use when a node comes too close to the edge of the canvas
        edgeMode: 'bounce', // warp or bounce
        // Node movement mode. Currently just one of ["linear", "pseudogrid"]
        movementType: 'pseudogrid',
        // Speed ratio
        speedRatio: .15,
        //backgroundColor: 'rgba(0,0,0,0.01)',
    });
    fx.start();

    root.addEventListener("click", (e) => {
        root.classList.toggle('hidden')
    });
});
