	<link rel="icon" href="<?php echo base_url("Atlantis/assets/img/icon.ico") ?>" type="image/x-icon" />

	<!-- Fonts and icons -->
	<script src="<?php echo base_url("Atlantis/assets/js/plugin/webfont/webfont.min.js") ?>"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Lato:300,400,700,900"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
				urls: ['<?php echo base_url("Atlantis/assets/css/fonts.min.css") ?>']
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="<?php echo base_url("Atlantis/assets/css/bootstrap.min.css") ?>">
	<link rel="stylesheet" href="<?php echo base_url("Atlantis/assets/css/atlantis.min.css") ?>">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="<?php echo base_url("Atlantis/assets/css/demo.css") ?>">

	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />