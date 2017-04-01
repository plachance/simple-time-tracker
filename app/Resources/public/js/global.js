$(document).ready(function()
{
	$('table.datatables').DataTable({
		pageLength: 25
	});
	$('select').select2({
		theme: "bootstrap",
		width: null //https://github.com/select2/select2-bootstrap-theme/issues/30
	});

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