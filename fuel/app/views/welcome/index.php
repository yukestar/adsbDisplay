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
	<form method=POST action="/flight/">
	  <select name="dest">
	    <?php foreach($destlist as $k => $v) { ?>
	    <option value="<?php print $v?>"<?php if ($dest == $v) { ?> SELECTED<?php } ?>><?php print $k?> - <?php print $v?></option>
	    <?php } ?>
	  </select>
	  <input type=submit value="send"> updated every 5 min.
	</form>
      </div>
      <div>
	<table class="table table-striped">
	  <thead class="thead-dark">
	    <tr>
	      <th scope="col">airline</th>
	      <th scope="col">flight no.</th>
	      <th scope="col">plane</th>
	      <th scope="col">reg no.</th>
	      <!-- <th scope="col">speed</th> -->
	      <!-- <th scope="col">alt</th> -->
	      <th scope="col">from</th>
	      <th scope="col">to</th>
	      <th scope="col">remain</th>
	      <th scope="col">last update</th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php foreach($flights as $k => $v) { ?>
            <?php // if (($v['length'] > 500) && ($v['destination_icao'] == $dest)) {continue;}?>
	    <?php // if ($v['altitude'] > 0) { ?>
	    <tr>
	      <td><?php print $v['carrier']?></td>
	      <td><a href="https://ja.flightaware.com/live/flight/<?php print $v['callsign']?>" target=_blank><?php print $v['callsign']?></a></td>
	      <td><?php print $v['aircraft']?></td>
	      <td><a href="https://flyteam.jp/registration/<?php print $v['registration']?>" target="_blank"><?php print $v['registration']?></a></td>
	      <!-- <td><?php print $v['speed']?> knot</td> -->
	      <!-- <td><?php print $v['altitude']?></td> -->
	      <td><a href="http://aircharterguide.com/AirportSearch.aspx?SearchText=<?php print $v['origin_icao']?>" target=_blank><?php print $v['origin_icao']?></a><br><?php print $v['origin_name']?></td>
	      <td><a href="http://aircharterguide.com/AirportSearch.aspx?SearchText=<?php print $v['destination_icao']?>" target=_blank><?php print $v['destination_icao']?></a><br><?php print $v['destination_name']?></td>
	      <td><?php print $v['length']?></td>
	      <td><?php print intval((strtotime($v['created']) - time()) / 60)?> min ago.</td>
	    </tr>
	      <?php } ?>
	      <?php // } ?>
	  </tbody>
	</table>
      </div>
      </div>
      <hr/>
      <footer>
	<div class="container">
	  <ul>
	    <li>updated every 5 minutes.</li>
	    <li>displays flights from last 20 minutes where destination is selected airport.</li>
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
