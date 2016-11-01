<?PHP
/*
  "phpkenosim" - PHP Keno Simulator (version 0.2.0)
  Copyright (C) 2016 masrourmouad https://github.com/masrourmouad/phpkenosim

  "phpkenosim" is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  "phpkenosim" is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with "phpkenosim"; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

    //Functions
    function keno_numbers_gen($num = 20)
    {
		$game = array();
		
		for($i=0;$i<$num;) 
		{ 
			$n = mt_rand(1,70); 
			if(!in_array($n, $game))
			{ 
				$game[] = $n;
				$i++; 
			} 
		}
		
		return $game;
	}
	
	function keno_multiplicateur()
	{
		$m = array(1 => 25, 2 => 37, 3 => 25, 4 => 10, 5 => 2, 10 => 1);
		$v = array();
		
		foreach($m as $key=>$val)
		{
			for($i=1;$i<=$val;$i++)
			{
				$v[] = $key;
			}
		}
		
		return $v;
	}
	
	function keno_ml_run($kn_ml)
	{
		return $kn_ml[mt_rand(0,count($kn_ml)-1)];
	}
	//------->

	//Variable Setup
	$winning = Array(// payout rules of keno
			"2"  => Array("0"=>"0","1"=>"0","2"=>"6"),
			"3"  => Array("0"=>"0","1"=>"0","2"=>"2","3"=>"10"),
			"4"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"5","4"=>"50"),
			"5"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"2","4"=>"10","5"=>"100"),
			"6"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"0","4"=>"2" ,"5"=>"30","6"=>"1000"),
			"7"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"0","4"=>"2" ,"5"=>"5" ,"6"=>"70" ,"7"=>"3000"),
			"8"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"0","4"=>"0" ,"5"=>"5" ,"6"=>"20" ,"7"=>"100","8"=>"10000"),
			"9"  => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"0","4"=>"1" ,"5"=>"2" ,"6"=>"8"  ,"7"=>"20" ,"8"=>"200" ,"9"=>"40000"),
			"10" => Array("0"=>"0","1"=>"0","2"=>"0","3"=>"0","4"=>"0" ,"5"=>"2" ,"6"=>"5"  ,"7"=>"15" ,"8"=>"100" ,"9"=>"2500","10"=>"200000")
	);
	
	
	$stat    = Array();
	$game    = Array();
	$player  = Array();
	$payout  = 0;
	$summery = array();
	$keno_ml = keno_multiplicateur();
	//------------------------->
	
	//Input
	//games to play //$_GET["games"];
	$games    = is_numeric(@$_POST["games"]) ? @$_POST["games"] : 1;
	$games    = ($games < 1) ? 10 : $games;
	
	// bet is currently set to 10
	$bet      = is_numeric(@$_POST["bet"]) ? @$_POST["bet"] : 1;
	$bet      = ($bet > 20 || $bet < 1) ? 1 : $bet;
	
	//number played by the player
	$gametype = is_numeric(@$_POST["gametype"]) ? @$_POST["gametype"] : 3;
	$gametype = ($gametype > 10 || $gametype < 2) ? 2 : $gametype;
	
	//same number
	$sn = (@$_POST["sn"] == "true") ? true : false;
	$mp = (@$_POST["mp"] == "true") ? true : false;
	//------->
	
	

	if(is_numeric($gametype+$bet+$games))
	{
		for ($a=1;$a<=$games;$a++) 
		{ 
			// if $games=10 then make 10 simulation
			$game     = keno_numbers_gen();
			$player   = ($sn == true && $a > 1) ? $player : keno_numbers_gen($gametype);
			$multpl   = ($mp == true) ? keno_ml_run($keno_ml) : 0;
			
			//$m=0;
			//for ($c=0;$c<$gametype;$c++) 
			//{ 
				//if(in_array($player[$c], $game)) 
				//{ 
					//$m++; 
				//} 
			//}
			
			$m = count(array_intersect($game, $player));
			$w = ($multpl == 0) ? 1 : $multpl;
			
			$stat[] = array($m, implode(" ", $player), (($winning[$gametype][$m]*$bet)*$w), $multpl);
			$payout += (($winning[$gametype][$m]*$bet)*$w);
		}
		
		
		
		$money_in = ($mp == true) ? (($games*$bet)*2) : ($games*$bet);
		$profit   = ($money_in <  $payout) ? ($payout-$money_in) : 0;
		$summery  = array($games,$money_in,$payout,$profit);
	}
	
	

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Keno Simulator</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href='style.css' rel='stylesheet' type='text/css'>  
	</head>
	
	<body>
		<center>
			<h1>Keno Simulator</h1>
			<br>
			<form method="post">
				<i style="color:red">Système Flash Uniquement</i>
				<table cellpadding="2" width="700px">
					<tr>
						<th>Mise</th>
						<th>Tirages</th>
						<th>Numéros</th>
						<th>Abonnement</th>
						<th>Multiplicateur</th>
					</tr>
					<tr>
						<td>
							<select name="bet">
							<?php 
								foreach(array("1", "2", "3", "5", "10", "20") as $sb)
								{
									echo '<option '.(($bet == $sb) ? "selected " : "").'value="'.$sb.'">'.$sb.' €</option>\r\n';
								}
							?>
							</select>
						</td>
						<td>
							<select name="games">
							<?php 
								foreach(range("1", "20") as $sg)
								{
									echo '<option '.(($games == $sg) ? "selected " : "").'value="'.$sg.'">'.$sg.' Tirage(s)</option>\r\n';
								}
							?>
							</select>
						</td>
						<td>
							<select name="gametype">
							<?php 
								foreach(range("2", "10") as $st)
								{
									echo '<option '.(($gametype == $st) ? "selected " : "").'value="'.$st.'">'.$st.' Numéro(s)</option>\r\n';
								}
							?>
							</select>
						</td>
						<td> Oui<input type="radio" value="true" <?php echo ($sn == true) ? "checked " : "" ?>name="sn">
						 Non<input type="radio" value="false" <?php echo ($sn == false) ? "checked " : "" ?>name="sn"></td>
						 </td>
						<td> Oui<input type="radio" value="true" <?php echo ($mp == true) ? "checked " : "" ?>name="mp">
						 Non<input type="radio" value="false" <?php echo ($mp == false) ? "checked " : "" ?>name="mp"></td>
						<td><input type="submit" value="Simuler"></td>
					</tr>
				</table>
			</form>
			<br>
			<?php 
					if(count($summery) > 0)
					{
						echo '<i style="color:red">Résumé: </i><br>';
						
						echo '<table cellpadding="5" width="700px">
							  <tr>
							  <th>Nombre de tirage</th>
							  <th>Mise totale</th>
							  <th>Total des gains</th> 
							  <th width="70px">Profit</th>
							  </tr>'; 
						
						 echo '<tr>
									<td>'.$summery[0].'</td>
									<td>'.$summery[1].' €</td>
									<td>'.$summery[2].' €</td>
									<td bgcolor="#EAEAEA"'.(($summery[3] > 0) ? " style='font-weight: bold'" : "").">".$summery[3].' €</td>
							  </tr>
							  </table>';
					}
					
			?>
			<br><br>
			 <i style="color:red">Répartition des gains: </i>
			 <br>
			<table cellpadding="5" width="700px">
				<tr>
				<th width="60px">Tirages</th>
				<th>N° cochés</th>
				<th width="100px">N° trouvés</th> 
				<th width="100px">Multiplicateur</th>
				<th width="70px">Gains</th>
				</tr>
				<?php
				
					//asort($stat);
					$g = 1;
					foreach($stat as $data)
					{
						echo "<tr><td><b>".$g."</b></td>".
							 "<td>".$data[1]."</td>".
							"<td>".$data[0]."</td>".
							"<td>".(($data[3] != 0) ? "X".$data[3] : "Sans")."</td>".
							"<td bgcolor='#EAEAEA'".(($data[2] > 0) ? " style='font-weight: bold'" : "").">".$data[2]." €</td></tr>\r\n";
						$g++;
					}
	
				?>
		   </table>
		 </center>
	</body>
</html>	
	
	
	
	
	
	
	
	
	
	
	
					
			
