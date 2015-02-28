jQuery(document).ready(function($)
{
	if (typeof Prototype != 'undefined' && Prototype.BrowserFeatures.ElementExtensions) {
		var disablePrototypeJS = function(method, pluginsToDisable) {
			var handler = function(event) {
				event.target[method] = undefined;
				setTimeout(function() {
					delete event.target[method];
				}, 0);
			};
			pluginsToDisable.each(function(plugin) {
				jQuery(window).on(method + '.bs.' + plugin, handler);
			});
		},
				pluginsToDisable = ['collapse', 'dropdown', 'modal', 'tooltip', 'popover', 'tab'];
		disablePrototypeJS('show', pluginsToDisable);
		disablePrototypeJS('hide', pluginsToDisable);
	}
	
	var initColorPicker = function(input, color)
	{
		if(input.data('cp-initialized') !== "true")
		{
			if(color === undefined || color === null)
			{
				color = '#47c3d3';
			}
			input.data('cp-initialized', "true");
			input.ColorPickerSliders({
				color: color,
				hsvpanel: true,
				previewformat: "hex"
			});
		}
		return input;
	};
	
	$(".colorpicker").each(function()
	{
		var $this = $(this);
		if($this.val() === "")
		{
			$this.click(function()
			{
				initColorPicker($this).trigger("colorpickersliders.show");
			});
		}
		else
		{
			initColorPicker($this, $this.val());
		}
	});
	
	$(".colorpicker").change(function()
	{
		var $this = $(this);
		if($this.val() === "")
		{
			$this.trigger("colorpickersliders.updateColor", "white").val("");
		}
	});
});