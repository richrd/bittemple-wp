
(function () {
  "use strict";

    const PIXEL_RATIO = (function () {
        var ctx = document.createElement("canvas").getContext("2d"),
            dpr = window.devicePixelRatio || 1,
            bsr = ctx.webkitBackingStorePixelRatio ||
                  ctx.mozBackingStorePixelRatio ||
                  ctx.msBackingStorePixelRatio ||
                  ctx.oBackingStorePixelRatio ||
                  ctx.backingStorePixelRatio || 1;

        return dpr / bsr;
    })();

    window.addEventListener('DOMContentLoaded', (event) => {
        initialize();
    });

    const initialize = () => {
        const fx = new CanvasNodeEffect({
            defaultNodeRadius: 3,
            edgeMode: 'bounce', // warp or bounce
            movementType: 'pseudogrid', // linear or pseudogrid
        });
        debug('initialized');
        fx.start();
        window.fx = fx;
    }

    function distance(node1, node2) {
        return Math.sqrt(
            (Math.pow(Math.abs(node1.x) - Math.abs(node2.x),2)) +
            (Math.pow(Math.abs(node1.y) - Math.abs(node2.y),2))
        )
    }

    const getRandomInt = (max) => {
        return Math.floor(Math.random() * Math.floor(max));
    }

    const debug = (...args) => {
        console.log(...args)
    }
    const applyOptions = (target, options) => {
        if (!options) {
            debug('no options provided');
            return;
        }

        for (const [key, value] of Object.entries(options)) {
            if (target[key] === undefined) {
                debug('invalid option:', key);
                continue;
            }

            target[key] = value;
        }
    }

    async function sleep(msec) {
        return new Promise(resolve => setTimeout(resolve, msec));
    }


    class Node {
        constructor(options) {
            this.x = 0;
            this.y = 0;
            this.speedRatio = 550;
            this.deltaRatio = 1;
            this.movementType = 'linear';

            applyOptions(this, options);


            this.speed = {
                x: ((getRandomInt(10)/this.speedRatio) + 0.02) * (getRandomInt(2) ? 1 : -1),
                y: ((getRandomInt(10)/this.speedRatio) + 0.02) * (getRandomInt(2) ? 1 : -1),
            }
        }

        update(delta) {
            delta = delta * this.deltaRatio;

            if (this.movementType === 'linear') {
                // Basic linear movement
                this.x += this.speed.x * delta;
                this.y += this.speed.y * delta;
            } else if (this.movementType === 'pseudogrid') {
                // Weird movement, I don't even know why this works
                // This creates weird orderly movement and structures in multiples of 45 degrees
                // Really captivating to watch
                //this.speed.y = Math.sin(this.x/20)/9;
                //this.speed.x = Math.sin(this.y/20)/9;

                this.x += this.speed.x * delta;
                this.y += this.speed.y * delta;

                // The small amount of randomness prevents nodes from getting stuck
                //this.speed.y = Math.sin(this.x / 20) / (20 + (getRandomInt(100) / 1000));
                //this.speed.x = Math.sin(this.y / 20) / (20 + (getRandomInt(100) / 1000));
                //const randomDivisor = 10;
                const randomDivisor = -2;
                this.speed.y = Math.sin(this.x / 20) / (20 + (Math.random() / 10));
                this.speed.x = Math.sin(this.y / 20) / (20 + (Math.random() / 10));
            }


            // This is similar to above but with a smaller grid size
            //this.speed.y = Math.sin(this.x/10)/9
            //this.speed.x = Math.cos(this.y/10)/9


            //this.speed.y = Math.sin(this.x/20+this.speed.x)/200
            //this.speed.x = Math.cos(this.y/20.1+this.speed.y)/200
        }
    }

    class CanvasNodeEffect {
        constructor(options) {
            this.running = false;
            this.paused = false;
            this.canvas = null;
            this.ctx = null;
            this.prevTime = null;
            this.frameRequestId = null;

            this.color = 'rgb(237, 255, 119)';
            this.backgroundColor = 'rgba(0, 0, 0, 1)';
            this.maxNodeCount = 200;
            this.minLinkDistance = 150;
            this.defaultNodeRadius = 3;
            this.edgeMode = 'bounce';
            this.edgePadding = 3.33;
            this.movementType = 'linear';
            this.lineWidth = 2;
            this.nodes = [];

            applyOptions(this, options);

            debug('constructed');
        }

        start() {
            debug('started')
            debug('run', this.running)
            this.running = true;
            debug('run', this.running)
            window.addEventListener('resize', (event) => {
                this.resize();
            });
            document.addEventListener("visibilitychange", (event) => {
                this.pause(document.hidden);
            });
            this.canvas = document.createElement('canvas')
            this.resize();
            this.generateNodes()
            this.ctx = this.canvas.getContext('2d');
            //this.ctx.setTransform(PIXEL_RATIO, 0, 0, PIXEL_RATIO, 0, 0);
            this.canvas.setAttribute('style', 'position:fixed;left:0;right:0;top:0;bottom:0;');
            this.canvas.setAttribute('id', 'canvas');
            document.body.appendChild(this.canvas);
            this.frameRequestId = window.requestAnimationFrame(this.step.bind(this));
        }

        stop() {
            this.running = false;
            window.cancelAnimationFrame(this.frameRequestId);
            document.body.appendChild(this.canvas);
        }

        pause(pause) {
            if (!this.running) {
                return;
            }

            pause = pause === undefined ? !this.paused : pause;

            debug("pause", pause ? "on" : "off");
            this.paused = pause;
            if (pause) {
                window.cancelAnimationFrame(this.frameRequestId)
            } else {
                this.prevTime = performance.now(); // Don't use the acumulated delta time for the next frame
                window.requestAnimationFrame(this.step.bind(this))
            }
        }

        generateNodes() {
            this.nodes = [];
            const areaRatio = 0.0001;
            const nodeCount = Math.floor(Math.min(
                this.maxNodeCount,
                (this.width * this.height) * areaRatio,
            ));

            let i = 0;
            while (i < nodeCount) {
                i += 1;
                const node = new Node({
                    movementType: this.movementType,
                });
                node.x = getRandomInt(this.width+1);
                node.y = getRandomInt(this.height+1);
                this.nodes.push(node);
            }

            debug("generated", this.nodes.length, "nodes")
        }

        resize() {
            this.width = window.innerWidth;
            this.height = window.innerHeight;
            this.canvas.setAttribute('width', this.width);
            this.canvas.setAttribute('height', this.height);
        }

        async step(timestamp) {
            if (this.paused) {
                return;
            }

            const delta = timestamp - this.prevTime;
            this.prevTime = timestamp;

            for (const node of this.nodes) {
                node.update(delta);
                if (this.edgeMode === 'warp') {
                    this.warpNode(node);
                } else {
                    this.bounceNode(node);
                }
            }

            await this.render();

            if (this.running) {
                window.requestAnimationFrame(this.step.bind(this));
            }
        }

        bounceNode(node) {
            if (node.x < 0) {
                node.x = this.edgePadding;
                node.speed.x *= -1;
            } else if (node.x > this.width) {
                node.x = this.width-this.edgePadding;
                node.speed.x *= -1
            }
            if (node.y < 0) {
                node.y = this.edgePadding;
                node.speed.y *= -1;
            } else if (node.y > this.height) {
                node.y = this.height-this.edgePadding;
                node.speed.y *= -1
            }
        }

        warpNode(node) {
            if (node.x < 0) {
                node.x = this.width-this.edgePadding;
            } else if (node.x > this.width) {
                node.x = this.edgePadding;
            }
            if (node.y < 0) {
                node.y = this.height-this.edgePadding;
            } else if (node.y > this.height) {
                node.y = this.edgePadding;
            }
        }

        async render() {
            // this.ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            // this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

            //this.canvas.width = this.canvas.width

            this.ctx.fillStyle = this.backgroundColor;
            this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
            this.ctx.lineWidth = this.lineWidth;

            let nodeList = [...this.nodes];

            let pairs = []

            for (const [i1, node] of this.nodes.entries()) {

                let closestDistance = null;

                for (const [i2, node2] of this.nodes.entries()) {
                    // Prevent cross checking a pair of nodes twice
                    if (i2 <= i1) {
                    //if (i1 > i2) {
                        continue;
                    }

                    let dist = distance(node, node2);

                    if (closestDistance === null || dist < closestDistance) {
                        closestDistance = dist;
                    }

                    if (dist < this.minLinkDistance) {
                        let opacity = 1 - ((dist / this.minLinkDistance));

                        //debug("DRAWING LINK", i1, i2, "mindist", this.minLinkDistance, "dist", dist, "opacity", opacity)

                        this.ctx.strokeStyle = 'rgba(237, 255, 119,' + opacity + ')';
                        //this.ctx.strokeStyle = 'rgba(237, 255, 119, .01)';
                        this.ctx.beginPath();
                        this.ctx.moveTo(node.x, node.y);
                        this.ctx.lineTo(node2.x, node2.y);
                        this.ctx.stroke();
                        //await sleep(1000)
                    }
                    //await sleep(1000)
                }


                //const opacity = closestDistance ? 1 -(closestDistance / this.minLinkDistance) * 2 : 0.1;
                let opacity = closestDistance
                    ? 1 -(closestDistance / this.minLinkDistance)
                    : 0.2;
                opacity = Math.max(opacity, 0.2);
                this.ctx.strokeStyle = 'hsla(68, 100%, 73%,' + opacity + ')';
                //this.ctx.strokeStyle = 'rgba(255,255,255,0.5)';
                //this.ctx.strokeStyle = 'rgba(255,0,0,0.5)';
                this.ctx.beginPath();
                this.ctx.arc(
                    node.x,
                    node.y,
                    this.defaultNodeRadius,
                    0,
                    2 * Math.PI
                );
                this.ctx.stroke();

                //await sleep(1000)
            }
        }

        // drawNode(node) {
        //     // TODO
        //     this.ctx.beginPath();
        //     // Fill with gradient
        //     this.ctx.strokeStyle = this.color;
        //     this.ctx.lineWidth = 2;
        //     this.ctx.arc(node.x, node.y, 10, 0, 2 * Math.PI);
        //     this.ctx.stroke();
        // }
    }
})();
