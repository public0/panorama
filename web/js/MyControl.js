function MyControls(camera, renderer){

	self = this;
	this.camera = camera;
	this.renderer = renderer;

	this.init = function() {
		controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.enablePan = false;
		controls.target.set( 0, 0, -1 );

		console.error(controls);
		return controls;
	}

}