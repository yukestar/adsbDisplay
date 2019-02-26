<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Flights To Here</title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php // echo Asset::css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'); ?>
  </head>
  <body>
    <header>
      <div class="container">
	<h1>
	  Flights To Here
	</h1>
      </div>
    </header>
    <div class="container">
      <div class="row">
	<p>Military flights reported with lat/lon <a href="/flight/">return</a></p>
	<table class="table table-striped">
	  <thead class="thead-dark">
	    <tr>
	      <th scope="col">aircraft</th>
	      <th scope="col">maker</th>
	      <th scope="col">country</th>
	      <th scope="col">altitude</th>
	      <th scope="col">latitude</th>
	      <th scope="col">longitude</th>
	      <th scope="col">speed</th>
	      <th scope="col">operator</th>
	      <th scope="col">registration</th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach($data as $flight) { ?>
	    <?php if (strlen($flight['aircraft']) == null) { continue; } ?>
	    <tr>
	      <td><?php print $flight['aircraft']?></td>
	      <td><?php print $flight['maker']?></td>
	      <td><?php print $flight['country']?></td>
	      <td><?php print $flight['altitude']?></td>
	      <td><?php print $flight['latitude']?></td>
	      <td><?php print $flight['longitude']?></td>
	      <td><?php print $flight['speed']?></td>
	      <td><?php print $flight['operator']?></td>
	      <td><?php print $flight['registration']?></td>
	    </tr>
	    <?php } ?>
	  </tbody>
	</table>
      </div>
    </div>
    <hr/>
      <footer>
	<div class="container">
	  <ul>
	    <li>updated every 5 minutes.</li>
	    <li>displays flights from last 20 minutes where origin/destination is selected airport.</li>
	    <li>remain(remaining distance) is not actual flight distance. </li>
	    <li>Powered by: <a href="https://www.adsbexchange.com/" target=_blank>adsbexchange.com</a>
	    <li>if anything my address is flightstohere at gmail ・ com</li>
	  </ul>
	  <a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="touch" title="このエントリーをはてなブックマークに追加"><img src="https://b.st-hatena.com/images/entry-button/button-only@2x.png" alt="このエントリーをはてなブックマークに追加" width="10" height="10" style="border: none;" /></a><script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
	</div>
      </footer>
    </div>
  </body>
</html>
      
