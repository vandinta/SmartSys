<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title>Dashboard - SmartSys</title>
		<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
		<?= $this->include("cms/layout/v_header") ?>
	</head>
	<body>
		<div class="wrapper sidebar_minimize">
			<!-- Navbar -->
			<?= $this->include("cms/layout/v_navbar") ?>
			<!-- End Navbar -->

			<!-- Sidebar -->
			<?= $this->include("cms/layout/v_sidebar") ?>
			<!-- End Sidebar -->

			<div class="main-panel">
				<div class="content">
					<?= $this->renderSection("content") ?>
				</div>
				
				<!--   Footer  -->
				<?= $this->include("cms/layout/v_footer") ?>
				<!--   Footer End  -->
			</div>
		</div>
		
		<!--  JS  -->
		<?= $this->include("cms/layout/v_js") ?>
		<?= $this->renderSection("content_js") ?>
		<!--  JS End  -->
	</body>
</html>