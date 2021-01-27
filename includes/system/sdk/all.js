/*!
  * SDK PACMEC v0.0.1 (https://managertechnology.com.co/)
  * Copyright 2020-2021 The SDK Authors (https://github.com/feliphegomez)
  * Licensed under MIT (https://managertechnology.com.co/)
  */

function Error(codigo, aviso, solucion){
	if(aviso == undefined) aviso = 'Error';
	if(solucion == undefined) solucion = '';
	this.error = true;
	this.code = codigo;
	this.code = codigo;
	this.message = aviso;
	this.solution = solucion;
};

function callB(a=null){ console.log(a); return a; }

class PACMEC {
	constructor() {
		if(!window.pacmec){ throw new Error(4200001, "window.pacmec.options no encontrado", "PACMEC.options"); }
		if(!window.pacmec.options.siteurl){ throw new Error(4200002, "window.pacmec.options.siteurl no encontrado", "PACMEC.options.siteurl"); }
		
		this.global = {
			version: "0.0.1",
			siteurl: window.pacmec.options.siteurl,
			API: null,
			CORE: null,
			url_api: window.pacmec.options.siteurl + '/api',
			url_core: window.pacmec.options.siteurl,
		}
		// console.log(JSON.stringify(location));
		
		this.global.API = axios.create({
			baseURL: this.global.url_api,
			withCredentials: true
		});
		
		this.global.API.interceptors.response.use(function (response) {
			if (response.headers['x-xsrf-token']) { document.cookie = 'XSRF-TOKEN=' + response.headers['x-xsrf-token'] + '; path=/'; }
			return response;
		});

		this.global.CORE = axios.create({
			baseURL: this.global.url_core,
		});
	}
	
	validateResponseAPI(re, call){
		if(call == undefined || typeof(call) !== 'function') call = callB;
		let errorEnabled = (re == null || re == undefined || re.status !== 200) ? true : (re.status == undefined || re.data == undefined || re.code !== undefined) ? true : false;
		let resp = {
			error: errorEnabled,
			status: (re.status !== undefined) ? re.status : 0,
			response: (re.data !== undefined) ? ((re.data.records !== undefined) ? re.data.records : re.data) : 0,
		};			
		return call(resp);
	}
	
	read_openapi(params, call){
		/*
		--- Lectura de datos ---
			PACMEC.read_openapi({}, (r)=>{
				// CODIGO AQUÍ
				console.log(r);
			});
		*/
		if(params == undefined) params = {};
		
		let re = null;
		
		this.global.API.get('/openapi', { params: params })
		.then((r) => { re = r; })
		.catch((e) => { re = e.response; })
		.finally(() => {
			this.validateResponseAPI(re, call);
		});
	}
	
	getLoginStatus(call){
		/*
			--- Verificacion de sesion ---
PACMEC.getLoginStatus((r)=>{
	console.log('r', r);
	if(r.error == false){
		
	}
	// Sesion OK
	if(s == true){
		console.log("Sesion iniciada.");
	} 
	// Sesion KO
	else {
		console.log("Debes iniciar sesion.");
	}
});
		*/
		var self = this;
		let re = null;
		self.global.API.get(self.global.url_api + '/me', { params: {} })
		.then((r) => { re = r; })
		.catch((e) => { re = e.response; })
		.finally(() => {
			self.validateResponseAPI(re, call);
		});
	}
	
	read(url, params, call){
		/*
		--- Lectura de datos ---
			PACMEC.read("url", {}, (r)=>{
				// CODIGO AQUÍ
				console.log(r);
			});
		*/
		if(url == undefined) url = '/null';
		if(params == undefined) params = {};		
		
		let re = null;
		
		this.global.API.get(this.global.siteurl + '/records' + url, { params: params })
		.then((r) => { re = r; })
		.catch((e) => { re = e.response; })
		.finally(() => {
			this.validateResponseAPI(re, call);
		});
	}
	
	create(url, params, call){
		/*
			--- Creación de datos ---
				PACMEC.create("url", {}, (r)=>{
					// CODIGO AQUÍ
					console.log(r);
				});
		*/
		var self = this;
		if(url == undefined) url = '/null';
		if(params == undefined) params = {};		
		var re = null;
		this.global.API.post(
			url == 'login' || url == '/login' || url == '/logout' || url == 'logout' || url == 'me' || url == '/me' || url == 'register' || url == '/register'  || url == 'password' || url == '/password' 
				? url 
				: '/records' + url
		, params)
		.then((r) => { console.log(r); re = r; })
		.catch((e) => { re = e.response; })
		.finally(() => {
			self.validateResponseAPI(re, call);
		});
	}
	
	login(username, hashNoCod, call){
		/*
			--- Iniciar sesion de forma manual ---
				PACMEC.login({username}, {password}, (r)=>{
					if(r.error === true){
						console.log("Datos invalidos");
					} else {
						console.log("Session iniciada");
						// CODIGO AQUÍ
					}
				});
		*/
		this.create("/login", {
			username: username,
			password: hashNoCod
		}, call);
	}
	
	create_user(params, call){
		if(params == undefined) params = {};
		/*
			--- Iniciar sesion de forma manual ---
				PACMEC.create_user({username}, {password}, (r)=>{
					if(r.error === true){
						console.log("Datos invalidos");
					} else {
						console.log("Session iniciada");
						// CODIGO AQUÍ
					}
				});
		*/
		params.activation_key = this.format.makeid(32);
		
		this.create("/register", params, call);
	}
	
	logout(){
		/*
			--- Cerrar sesion de forma manual ---
				PACMEC.logout((r)=>{
						// CODIGO AQUÍ
				});
		*/
		this.create("/logout", {}, location.reload());
	}
	
	format = {
		zfill(number, width) {
			var numberOutput = Math.abs(number);
			var length = number.toString().length;
			var zero = "0";
			if (width <= length) {
				if (number < 0) { return ("-" + numberOutput.toString()); }
				else { return numberOutput.toString(); }
			} else {
				if (number < 0) { return ("-" + (zero.repeat(width - length)) + numberOutput.toString()); }
				else { return ((zero.repeat(width - length)) + numberOutput.toString()); }
			}
		},
		formatMoney(number, decPlaces, decSep, thouSep) {
			var self = this;
			decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
			decSep = typeof decSep === "undefined" ? "." : decSep;
			thouSep = typeof thouSep === "undefined" ? "," : thouSep;
			var sign = number < 0 ? "-" : "";
			var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
			var j = (j = i.length) > 3 ? j % 3 : 0;
			return sign + (j ? i.substr(0, j) + thouSep : "") + (i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + (thouSep))) + (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
		},
		makeid(length) {
		   var result           = '';
		   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		   var charactersLength = characters.length;
		   for ( var i = 0; i < length; i++ ) {
			  result += characters.charAt(Math.floor(Math.random() * charactersLength));
		   }
		   return result;
		}
	}
	
	getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}
	
	change(url, ref, params, call){
		/*
			--- Creación de datos ---
				PACMEC.change("url", {}, (r)=>{
					// CODIGO AQUÍ
					console.log(r);
				});
		*/
		var self = this;
		if(url == undefined) url = '/null';
		if(ref == undefined) ref = 0;
		if(params == undefined) params = {};		
		var re = null;
		this.global.API.post(
			url == 'login' || url == '/login' || url == '/logout' || url == 'logout' || url == 'me' || url == '/me' || url == 'register' || url == '/register'  || url == 'password' || url == '/password' 
				? url 
				: '/records' + url
		, params)
		.then((r) => { console.log(r); re = r; })
		.catch((e) => { re = e.response; })
		.finally(() => {
			self.validateResponseAPI(re, call);
		});
	}
	
}

try {
	//console.log(JSON.stringify(PACMEC));
	if(!window.PACMEC){
		// throw new Error(4200001, "PACMEC no iniciado", "PACMEC.init");
		PACMEC = window.PACMEC = new PACMEC();
	}
	var global = {
		__type: 'JS_SDK_SANDBOX',
		window: window,
		document: window.document
	};	
	console.log("'SDK termino de cargar FIN!");
  
	// menu toggle button
	function editThisRoute() {
		
		function myPluginHello(editor){
			editor.BlockManager.add('my-first-block', {
				label: 'Simple block',
				content: '<div class="my-block">This is a simple block</div>',
			});
		}
		
		var lp = './img/';
		var plp = '//placehold.it/350x250/';
		var images = [
			lp+'team1.jpg', lp+'team2.jpg', lp+'team3.jpg', plp+'78c5d6/fff/image1.jpg', plp+'459ba8/fff/image2.jpg', plp+'79c267/fff/image3.jpg',
			plp+'c5d647/fff/image4.jpg', plp+'f28c33/fff/image5.jpg', plp+'e868a2/fff/image6.jpg', plp+'cc4360/fff/image7.jpg',
			lp+'work-desk.jpg', lp+'phone-app.png', lp+'bg-gr-v.png'
		];
		var editor = grapesjs.init({
			avoidInlineStyle: 1,
			height: '90%',
			container: '#gjs',
			fromElement: true,
			showOffsets: 1,
			assetManager: {
			  embedAsBase64: 1,
			  assets: images
			},
			selectorManager: { componentFirst: true },
			styleManager: { clearProperties: 1 },
			noticeOnUnload: 0,
			storageManager: { autoload: 0 },
			canvas: {
			  scripts: scripts_list,
			  styles: styles_list
			},
			i18n: {
			   locale: 'es',
			   localeFallback: 'es',
			},
			plugins: [
				'grapesjs-plugin-bootstrap',
				'grapesjs-tui-image-editor',
				'grapesjs-lory-slider',
				'grapesjs-tabs',
				'grapesjs-custom-code',
				'grapesjs-touch',
				'grapesjs-parser-postcss',
				'grapesjs-tooltip',
				'grapesjs-typed',
				'grapesjs-style-bg',
				'gjs-preset-webpage',
				'grapesjs-plugin-forms',
				//'grapesjs-preset-newsletter',
				//'grapesjs-plugin-ckeditor',
				'grapesjs-component-countdown',
			],
			pluginsOpts: {
				
				'grapesjs-plugin-bootstrap': {
					addBasicStyle: true
				},
				'grapesjs-tui-image-editor': {
					config: {
						includeUI: {
							initMenu: 'filter',
						},
					},
					icons: {
					},
				},
				'grapesjs-lory-slider': {
					sliderBlock: {
					  category: 'Extra'
					}
				  },
				'grapesjs-tabs': {
					tabsBlock: {
					  category: 'Extra'
					}
				},
				'grapesjs-typed': {
					block: {
					  category: 'Extra',
					  content: {
						type: 'typed',
						'type-speed': 40,
						strings: [
						  'Text row one',
						  'Text row two',
						  'Text row three',
						],
					  }
					}
				},
				'gjs-preset-webpage': {
					modalImportTitle: 'Import Template',
					modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
					modalImportContent: function(editor) {
					  return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
					},
					filestackOpts: null, //{ key: 'AYmqZc2e8RLGLE7TGkX3Hz' },
					aviaryOpts: false,
					blocksBasicOpts: { flexGrid: 1 },
					customStyleManager: [{
					  name: 'General',
					  buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
					  properties:[{
						  name: 'Alignment',
						  property: 'float',
						  type: 'radio',
						  defaults: 'none',
						  list: [
							{ value: 'none', className: 'fa fa-times'},
							{ value: 'left', className: 'fa fa-align-left'},
							{ value: 'right', className: 'fa fa-align-right'}
						  ],
						},
						{ property: 'position', type: 'select'}
					  ],
					},{
						name: 'Dimension',
						open: false,
						buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
						properties: [{
						  id: 'flex-width',
						  type: 'integer',
						  name: 'Width',
						  units: ['px', '%'],
						  property: 'flex-basis',
						  toRequire: 1,
						},{
						  property: 'margin',
						  properties:[
							{ name: 'Top', property: 'margin-top'},
							{ name: 'Right', property: 'margin-right'},
							{ name: 'Bottom', property: 'margin-bottom'},
							{ name: 'Left', property: 'margin-left'}
						  ],
						},{
						  property  : 'padding',
						  properties:[
							{ name: 'Top', property: 'padding-top'},
							{ name: 'Right', property: 'padding-right'},
							{ name: 'Bottom', property: 'padding-bottom'},
							{ name: 'Left', property: 'padding-left'}
						  ],
						}],
					  },{
						name: 'Typography',
						open: false,
						buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
						properties:[
						  { name: 'Font', property: 'font-family'},
						  { name: 'Weight', property: 'font-weight'},
						  { name:  'Font color', property: 'color'},
						  {
							property: 'text-align',
							type: 'radio',
							defaults: 'left',
							list: [
							  { value : 'left',  name : 'Left',    className: 'fa fa-align-left'},
							  { value : 'center',  name : 'Center',  className: 'fa fa-align-center' },
							  { value : 'right',   name : 'Right',   className: 'fa fa-align-right'},
							  { value : 'justify', name : 'Justify',   className: 'fa fa-align-justify'}
							],
						  },{
							property: 'text-decoration',
							type: 'radio',
							defaults: 'none',
							list: [
							  { value: 'none', name: 'None', className: 'fa fa-times'},
							  { value: 'underline', name: 'underline', className: 'fa fa-underline' },
							  { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
							],
						  },{
							property: 'text-shadow',
							properties: [
							  { name: 'X position', property: 'text-shadow-h'},
							  { name: 'Y position', property: 'text-shadow-v'},
							  { name: 'Blur', property: 'text-shadow-blur'},
							  { name: 'Color', property: 'text-shadow-color'}
							],
						}],
					  },{
						name: 'Decorations',
						open: false,
						buildProps: ['opacity', 'border-radius', 'border', 'box-shadow', 'background-bg'],
						properties: [{
						  type: 'slider',
						  property: 'opacity',
						  defaults: 1,
						  step: 0.01,
						  max: 1,
						  min:0,
						},{
						  property: 'border-radius',
						  properties  : [
							{ name: 'Top', property: 'border-top-left-radius'},
							{ name: 'Right', property: 'border-top-right-radius'},
							{ name: 'Bottom', property: 'border-bottom-left-radius'},
							{ name: 'Left', property: 'border-bottom-right-radius'}
						  ],
						},{
						  property: 'box-shadow',
						  properties: [
							{ name: 'X position', property: 'box-shadow-h'},
							{ name: 'Y position', property: 'box-shadow-v'},
							{ name: 'Blur', property: 'box-shadow-blur'},
							{ name: 'Spread', property: 'box-shadow-spread'},
							{ name: 'Color', property: 'box-shadow-color'},
							{ name: 'Shadow type', property: 'box-shadow-type'}
						  ],
						},{
						  id: 'background-bg',
						  property: 'background',
						  type: 'bg',
						},],
					  },{
						name: 'Extra',
						open: false,
						buildProps: ['transition', 'perspective', 'transform'],
						properties: [{
						  property: 'transition',
						  properties:[
							{ name: 'Property', property: 'transition-property'},
							{ name: 'Duration', property: 'transition-duration'},
							{ name: 'Easing', property: 'transition-timing-function'}
						  ],
						},{
						  property: 'transform',
						  properties:[
							{ name: 'Rotate X', property: 'transform-rotate-x'},
							{ name: 'Rotate Y', property: 'transform-rotate-y'},
							{ name: 'Rotate Z', property: 'transform-rotate-z'},
							{ name: 'Scale X', property: 'transform-scale-x'},
							{ name: 'Scale Y', property: 'transform-scale-y'},
							{ name: 'Scale Z', property: 'transform-scale-z'}
						  ],
						}]
					  },{
						name: 'Flex',
						open: false,
						properties: [{
						  name: 'Flex Container',
						  property: 'display',
						  type: 'select',
						  defaults: 'block',
						  list: [
							{ value: 'block', name: 'Disable'},
							{ value: 'flex', name: 'Enable'}
						  ],
						},{
						  name: 'Flex Parent',
						  property: 'label-parent-flex',
						  type: 'integer',
						},{
						  name      : 'Direction',
						  property  : 'flex-direction',
						  type    : 'radio',
						  defaults  : 'row',
						  list    : [{
									value   : 'row',
									name    : 'Row',
									className : 'icons-flex icon-dir-row',
									title   : 'Row',
								  },{
									value   : 'row-reverse',
									name    : 'Row reverse',
									className : 'icons-flex icon-dir-row-rev',
									title   : 'Row reverse',
								  },{
									value   : 'column',
									name    : 'Column',
									title   : 'Column',
									className : 'icons-flex icon-dir-col',
								  },{
									value   : 'column-reverse',
									name    : 'Column reverse',
									title   : 'Column reverse',
									className : 'icons-flex icon-dir-col-rev',
								  }],
						},{
						  name      : 'Justify',
						  property  : 'justify-content',
						  type    : 'radio',
						  defaults  : 'flex-start',
						  list    : [{
									value   : 'flex-start',
									className : 'icons-flex icon-just-start',
									title   : 'Start',
								  },{
									value   : 'flex-end',
									title    : 'End',
									className : 'icons-flex icon-just-end',
								  },{
									value   : 'space-between',
									title    : 'Space between',
									className : 'icons-flex icon-just-sp-bet',
								  },{
									value   : 'space-around',
									title    : 'Space around',
									className : 'icons-flex icon-just-sp-ar',
								  },{
									value   : 'center',
									title    : 'Center',
									className : 'icons-flex icon-just-sp-cent',
								  }],
						},{
						  name      : 'Align',
						  property  : 'align-items',
						  type    : 'radio',
						  defaults  : 'center',
						  list    : [{
									value   : 'flex-start',
									title    : 'Start',
									className : 'icons-flex icon-al-start',
								  },{
									value   : 'flex-end',
									title    : 'End',
									className : 'icons-flex icon-al-end',
								  },{
									value   : 'stretch',
									title    : 'Stretch',
									className : 'icons-flex icon-al-str',
								  },{
									value   : 'center',
									title    : 'Center',
									className : 'icons-flex icon-al-center',
								  }],
						},{
						  name: 'Flex Children',
						  property: 'label-parent-flex',
						  type: 'integer',
						},{
						  name:     'Order',
						  property:   'order',
						  type:     'integer',
						  defaults :  0,
						  min: 0
						},{
						  name    : 'Flex',
						  property  : 'flex',
						  type    : 'composite',
						  properties  : [{
								  name:     'Grow',
								  property:   'flex-grow',
								  type:     'integer',
								  defaults :  0,
								  min: 0
								},{
								  name:     'Shrink',
								  property:   'flex-shrink',
								  type:     'integer',
								  defaults :  0,
								  min: 0
								},{
								  name:     'Basis',
								  property:   'flex-basis',
								  type:     'integer',
								  units:    ['px','%',''],
								  unit: '',
								  defaults :  'auto',
								}],
						},{
						  name      : 'Align',
						  property  : 'align-self',
						  type      : 'radio',
						  defaults  : 'auto',
						  list    : [{
									value   : 'auto',
									name    : 'Auto',
								  },{
									value   : 'flex-start',
									title    : 'Start',
									className : 'icons-flex icon-al-start',
								  },{
									value   : 'flex-end',
									title    : 'End',
									className : 'icons-flex icon-al-end',
								  },{
									value   : 'stretch',
									title    : 'Stretch',
									className : 'icons-flex icon-al-str',
								  },{
									value   : 'center',
									title    : 'Center',
									className : 'icons-flex icon-al-center',
								  }],
						}]
					  }
					],
				},
			}
		});
		
		editor.I18n.addMessages({
			es: {
				styleManager: {
					properties: {
						'background-repeat': 'Repetir',
						'background-position': 'Posicion',
						'background-attachment': 'Attachment',
						'background-size': 'Tamaño',
					}
				},
			}
		});
		
		
		var pn = editor.Panels;
		var modal = editor.Modal;
		var cmdm = editor.Commands;
		cmdm.add('canvas-clear', function() {
		if(confirm('Areeee you sure to clean the canvas?')) {
		var comps = editor.DomComponents.clear();
		setTimeout(function(){ localStorage.clear()}, 0)
		}
		});
		cmdm.add('set-device-desktop', {
		run: function(ed) { ed.setDevice('Desktop') },
		stop: function() {},
		});
		cmdm.add('set-device-tablet', {
		run: function(ed) { ed.setDevice('Tablet') },
		stop: function() {},
		});
		cmdm.add('set-device-mobile', {
		run: function(ed) { ed.setDevice('Mobile portrait') },
		stop: function() {},
		});
		
		// Add info command
		var mdlClass = 'gjs-mdl-dialog-sm';
		var infoContainer = document.getElementById('info-panel');
		cmdm.add('open-info', function() {
		var mdlDialog = document.querySelector('.gjs-mdl-dialog');
		mdlDialog.className += ' ' + mdlClass;
		infoContainer.style.display = 'block';
		modal.setTitle('About this demo');
		modal.setContent(infoContainer);
		modal.open();
		modal.getModel().once('change:open', function() {
		mdlDialog.className = mdlDialog.className.replace(mdlClass, '');
		})
		});
		pn.addButton('options', {
		id: 'open-info',
		className: 'fa fa-question-circle',
		command: function() { editor.runCommand('open-info') },
		attributes: {
		'title': 'About',
		'data-tooltip-pos': 'bottom',
		},
		});
		
		// Simple warn notifier
		var origWarn = console.warn;
		toastr.options = {
		closeButton: true,
		preventDuplicates: true,
		showDuration: 250,
		hideDuration: 150
		};
		console.warn = function (msg) {
		if (msg.indexOf('[undefined]') == -1) {
		toastr.warning(msg);
		}
		origWarn(msg);
		};
		
		
		// Add and beautify tooltips
		/*
		[['sw-visibility', 'Show Borders'], ['preview', 'Preview'], ['fullscreen', 'Fullscreen'],
		['export-template', 'Export'], ['undo', 'Undo'], ['redo', 'Redo'],
		['gjs-open-import-webpage', 'Import'], ['canvas-clear', 'Clear canvas']]
		.forEach(function(item) {
		pn.getButton('options', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
		});
		[['open-sm', 'Style Manager'], ['open-layers', 'Layers'], ['open-blocks', 'Blocks']]
		.forEach(function(item) {
		pn.getButton('views', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
		});
		var titles = document.querySelectorAll('*[title]');

		for (var i = 0; i < titles.length; i++) {
		var el = titles[i];
		var title = el.getAttribute('title');
		title = title ? title.trim(): '';
		if(!title)
		  break;
		el.setAttribute('data-tooltip', title);
		el.setAttribute('title', '');
		}*/

		// Show borders by default
		pn.getButton('options', 'sw-visibility').set('active', 1);


		// Store and load events
		editor.on('storage:load', function(e) { console.log('Loaded ', e) });
		editor.on('storage:store', function(e) { console.log('Stored ', e) });



	  editor.Panels.addButton('options',
		[{
		  id: 'save-db',
		  className: 'fa fa-floppy-o',
		  command: 'save-db',
		  attributes: {title: 'Save DB'}
		}]
	  );
		  

		// Add the command
		editor.Commands.add
		('save-db',
		{
			run: function(editor, sender)
			{
			  sender && sender.set('active', 0); // turn off the button
			  editor.store();

			  var htmldata = editor.getHtml();
			  var cssdata = editor.getCss();
			  var jsdata = editor.getJs();
			  console.log(htmldata);
			  console.log(cssdata);
			  console.log(jsdata);
			  /*
			  PACMEC.change('/routes', route.id, {
				  content: ''
			  });*/
			}
		});
		
		// Do stuff on load
		editor.on('load', function() {
		var $ = grapesjs.$;

		// Show logo with the version
		var logoCont = document.querySelector('.gjs-logo-cont');
		document.querySelector('.gjs-logo-version').innerHTML = 'v' + grapesjs.version;
		var logoPanel = document.querySelector('.gjs-pn-commands');
		logoPanel.appendChild(logoCont);


		// Load and show settings and style manager
		var openTmBtn = pn.getButton('views', 'open-tm');
		openTmBtn && openTmBtn.set('active', 1);
		var openSm = pn.getButton('views', 'open-sm');
		openSm && openSm.set('active', 1);

		// Add Settings Sector
		var traitsSector = $('<div class="gjs-sm-sector no-select">'+
		  '<div class="gjs-sm-title"><span class="icon-settings fa fa-cog"></span> Settings</div>' +
		  '<div class="gjs-sm-properties" style="display: none;"></div></div>');
		var traitsProps = traitsSector.find('.gjs-sm-properties');
		traitsProps.append($('.gjs-trt-traits'));
		$('.gjs-sm-sectors').before(traitsSector);
		traitsSector.find('.gjs-sm-title').on('click', function(){
		  var traitStyle = traitsProps.get(0).style;
		  var hidden = traitStyle.display == 'none';
		  if (hidden) {
			traitStyle.display = 'block';
		  } else {
			traitStyle.display = 'none';
		  }
		});

		// Open block manager
		var openBlocksBtn = editor.Panels.getButton('views', 'open-blocks');
		openBlocksBtn && openBlocksBtn.set('active', 1);

		// Move Ad
		$('#gjs').append($('.ad-cont'));
		});

		window.editor = editor;
	}
} catch (e) {
	console.error("PACMEC::Error - FIN");
	console.log(e);
}
