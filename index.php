<?php require_once 'form.php';?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Form Manager</title>
		<style type="text/css">
			.link {
				display: block;
				padding: 2px;
				margin: 5px 1px;
			}

			a {
				display: inline-block;
				margin: 0 5px;
				color: cornflowerblue;
			}

			a:hover {
				text-decoration: none;
			}

			.accent,
			a.accent {
				color: red;
				font-size: 0.9em;
				line-height: 1.5;
			}

			h1 {
				font-size: 1em;
				color: #000000;
				margin: 0;
				padding: 5px 0;
				text-transform: uppercase;
			}
		</style>
	</head>

	<body>
		<h1>The Data</h1>
		<?php echo oForm::frontend();?>
	</body>

</html>