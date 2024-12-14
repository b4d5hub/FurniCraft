function SoberCustomCssModule() {
	var self = this;

	self.init = function () {
		elementor.hooks.addFilter('editor/style/styleText', self.addCustomCss);

		elementor.settings.page.model.on('change', self.addPageCustomCss);

		elementor.on('preview:loaded', self.addPageCustomCss);
	};

	self.addPageCustomCss = function () {
		var customCSS = elementor.settings.page.model.get('custom_css');

		if (customCSS) {
			customCSS = customCSS.replace(/selector/g, elementor.config.settings.page.cssWrapperSelector);

			elementor.settings.page.getControlsCSS().elements.$stylesheetElement.append(customCSS);
		}
	};

	self.addCustomCss = function (css, view) {
		if ( ! view || typeof view.getEditModel === 'undefined' ) {
			return css;
		}

		var model = view.getEditModel(),
		    customCSS = model.get('settings').get('custom_css');

		if (customCSS) {
			css += customCSS.replace(/selector/g, '.elementor-element.elementor-element-' + view.model.id);
		}

		return css;
	};

	self.init();
}

jQuery( window ).on( 'elementor:init', function() {
	if ( typeof ElementorProConfig === 'undefined' ) {
		new SoberCustomCssModule();
	}
} );