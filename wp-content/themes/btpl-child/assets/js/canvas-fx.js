
(function () {
  "use strict";

  const DEBUG = false;

    const distance = (node1, node2) => {
        return Math.sqrt(
            (Math.pow(Math.abs(node1.x) - Math.abs(node2.x),2)) +
            (Math.pow(Math.abs(node1.y) - Math.abs(node2.y),2))
        )
    }

    const getRandomInt = (max) => {
        return Math.floor(Math.random() * Math.floor(max));
    }

    const debug = (...args) => {
        if (!DEBUG) {
            return;
        }
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

    class Node {
        constructor(options) {
            this.x = 0;
            this.y = 0;
            this.speedRatio = 550;
            this.movementType = 'linear';

            this.movementTypes= {
                linear: (delta) => {
                    // Basic linear movement
                    this.x += this.speed.x * delta;
                    this.y += this.speed.y * delta;
                },
                pseudogrid: (delta) => {
                    // Weird movement, I don't even know why this works.
                    // This creates weird orderly movement and structures in
                    // multiples of 45 degrees with a grid like feel.
                    // Really captivating to watch.
                    this.x += this.speed.x * delta;
                    this.y += this.speed.y * delta;

                    // The small amount of randomness prevents nodes from getting stuck
                    const randomDivisor = -10;
                    const scale = 20;

                    this.speed.x = Math.sin((this.y+Math.random()) / scale) / (20 + (Math.random() / randomDivisor));
                    this.speed.y = Math.sin((this.x+Math.random()) / scale) / (20 + (Math.random() / randomDivisor));
                }
            }

            applyOptions(this, options);

            this.speed = {
                x: ((getRandomInt(10)/this.speedRatio) + 0.02) * (getRandomInt(2) ? 1 : -1),
                y: ((getRandomInt(10)/this.speedRatio) + 0.02) * (getRandomInt(2) ? 1 : -1),
            }
        }

        update(delta) {
            this.movementTypes[this.movementType](delta)
        }
    }

    class CanvasNodeEffect {
        constructor(options) {
            // Internals
            this.running = false;
            this.paused = false;
            this.canvas = null;
            this.ctx = null;
            this.prevTime = null;
            this.frameRequestId = null;
            this.nodes = [];

            // Settable options
            this.canvasSelector = '#canvas';
            this.backgroundColor = null;
            this.speedRatio = 1;
            this.color = [237, 255, 119];
            this.maxNodeCount = 150;
            this.minLinkDistance = 150;
            this.nodeRadius = 8;
            this.edgeMode = 'bounce';
            this.edgePadding = 3.33;
            this.movementType = 'linear';
            this.linkLineWidth = 3;
            this.nodeLineWidth = 3;
            this.nodeToAreaRatio = 0.0001;

            applyOptions(this, options);

            debug('constructed');
        }

        start() {
            this.running = true;
            debug('started');

            // Register event handlers
            window.addEventListener('resize', (event) => {
                // Resize canvas to window size
                this.resize();
            });
            document.addEventListener("visibilitychange", (event) => {
                // Pause when browser tab is not active
                this.pause(document.hidden);
            });

            // Create the canvas
            this.canvas = document.querySelector(this.canvasSelector)
            document.body.appendChild(this.canvas);

            // Set the canvas size
            this.resize();

            // Generate nodes based on screen size
            this.generateNodes()

            // Create the 2d rendering context
            this.ctx = this.canvas.getContext('2d');

            // Start the animation and retain
            this.requestAnimationFrame();
        }

        requestAnimationFrame() {
            this.frameRequestId = window.requestAnimationFrame(this.step.bind(this));
        }

        stop() {
            this.running = false;
            window.cancelAnimationFrame(this.frameRequestId);
        }

        pause(pause) {
            // Inhibit pausing if not running since that'd make no sense
            if (!this.running) {
                return;
            }

            pause = pause === undefined ? !this.paused : pause;

            debug("pause:", pause ? "on" : "off");

            this.paused = pause;
            if (pause) {
                window.cancelAnimationFrame(this.frameRequestId);
            } else {
                this.prevTime = performance.now(); // Don't use the acumulated delta time for the next frame
                this.requestAnimationFrame();
            }
        }

        generateNodes() {
            this.nodes = [];

            // How many nodes to generate, determined by screen size and clamped to a max of
            const nodeCount = Math.floor(Math.min(
                this.maxNodeCount,
                (this.width * this.height) * this.nodeToAreaRatio,
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

        step(timestamp) {
            if (this.paused) {
                return;
            }

            const delta = (timestamp - this.prevTime) * this.speedRatio;
            this.prevTime = timestamp;

            for (const node of this.nodes) {
                node.update(delta);
                if (this.edgeMode === 'warp') {
                    this.warpNode(node);
                } else {
                    this.bounceNode(node);
                }
            }

            this.render();

            if (this.running) {
                window.requestAnimationFrame(this.step.bind(this));
            }
        }

        bounceNode(node) {
            if (node.x < this.edgePadding) {
                node.x = this.edgePadding;
                node.speed.x *= -1;
            } else if (node.x > this.width-this.edgePadding) {
                node.x = this.width-this.edgePadding;
                node.speed.x *= -1
            }
            if (node.y < this.edgePadding) {
                node.y = this.edgePadding;
                node.speed.y *= -1;
            } else if (node.y > this.height-this.edgePadding) {
                node.y = this.height-this.edgePadding;
                node.speed.y *= -1
            }
        }

        warpNode(node) {
            if (node.x < this.edgePadding) {
                node.x = this.width-this.edgePadding;
            } else if (node.x > this.width-this.edgePadding) {
                node.x = this.edgePadding;
            }
            if (node.y < this.edgePadding) {
                node.y = this.height-this.edgePadding;
            } else if (node.y > this.height-this.edgePadding) {
                node.y = this.edgePadding;
            }
        }

        async render() {
            if (this.backgroundColor) {
                this.ctx.fillStyle = this.backgroundColor;
                this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
            } else {
                this.canvas.width = this.canvas.width
            }

            for (const [i1, node] of this.nodes.entries()) {

                let closestDistance = null;


                for (const [i2, node2] of this.nodes.entries()) {
                    // Prevent cross checking a pair of nodes twice
                    if (i2 <= i1) {
                        continue;
                    }

                    let dist = distance(node, node2);

                    if (closestDistance === null || dist < closestDistance) {
                        closestDistance = dist;
                    }

                    if (dist < this.minLinkDistance) {
                        let opacity = 1 - ((dist / this.minLinkDistance));
                        this.ctx.strokeStyle = `rgba(${this.color[0]}, ${this.color[1]}, ${this.color[2]}, ${opacity})`;
                        this.ctx.lineWidth = this.linkLineWidth;
                        this.ctx.beginPath();
                        this.ctx.moveTo(node.x, node.y);
                        this.ctx.lineTo(node2.x, node2.y);
                        this.ctx.stroke();
                    }
                }

                const opacity = Math.max(closestDistance ? 1 -(closestDistance / this.minLinkDistance) : 0.2, 0.2);
                this.ctx.lineWidth = this.nodeLineWidth;
                this.ctx.fillStyle = "transparent";
                this.ctx.strokeStyle = `rgba(${this.color[0]}, ${this.color[1]}, ${this.color[2]}, ${opacity})`;
                this.ctx.beginPath();
                this.ctx.arc(node.x, node.y, this.nodeRadius, 0, Math.PI*2);
                this.ctx.closePath();
                this.ctx.fill();
                this.ctx.stroke();
            }
        }
    }
    window.canvasFx = {
        nodeFx: CanvasNodeEffect,
    }
})();
