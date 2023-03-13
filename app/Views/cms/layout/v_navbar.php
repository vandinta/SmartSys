		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				<a href="index.html" class="logo">
					<img src="<?php echo base_url("Atlantis/assets/img/logo.svg") ?>" alt="navbar brand" class="navbar-brand">
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
				<div class="container-fluid">
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="<?php if ($_SESSION['profile_picture'] == null) {
															echo base_url("assets/image/profile/default.png");
														} else {
															echo base_url("assets/image/profile/" . $_SESSION['profile_picture']);
														} ?>" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="<?php if ($_SESSION['profile_picture'] == null) {
																													echo base_url("assets/image/profile/default.png");
																												} else {
																													echo base_url("assets/image/profile/" . $_SESSION['profile_picture']);
																												} ?>" alt="image profile" class="avatar-img rounded"></div>
											<div class="u-text">
												<h4><?= $_SESSION['username']; ?></h4>
												<p class="text-muted"><?= $_SESSION['email']; ?></p>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?= base_url("/setting") . "/" . $_SESSION['email'] ?>">Pengaturan Akun</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?php echo base_url("/logout") ?>">Logout</a>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</div>