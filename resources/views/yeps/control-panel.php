<!doctype html>
<html>
<head>
	<title>Yeplive Control Panel</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
</head>
<body>
	<div class="container">
		<h1>Yeplive Control Panel</h1>
		<div class="row">
			<button class="btn btn-primary">Yeps</button>
			<div id="data"></div>
		</div>
	</div>

<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
var yeps = [];
$(function(){
	function getYeps(){
		$.get('/api/v1/yeps').then(function(res){
			res.yeps.forEach(function(data){
				yeps.push(new Yep(data));
			});
		}, function(err){

		});
	}

	function hideYep(){

	}

	function createYep(){

	}

	function displayYeps(yeps){
		yeps.forEach(displayYep);
	}

	function displayYep(yep){
		
	}

	function Yep(attrs){
		this.attrs = attrs;
	}


	getYeps();

});
</script>

</body>
</html>
