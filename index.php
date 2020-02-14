<?php
session_start();
$_SESSION['logged']=false;

//usual cleanup, key value coders know what I do mean

if (!file_exists('./data/tokens'))
	mkdir('./data/tokens');
	
$tokens=array_diff(scandir('./data/tokens'), Array ('..', '.'));

foreach ($tokens as $token)
		if (floatval($token)+10*60<microtime(true))
			unlink ('./data/tokens/'.$token);

//end of cleanup 

$lug_name='GUL de la Petite Montagne';
$lug_email='gul@petite-montagne.net';

$server_address='http://gul.petite-montagne.net/';

$geoloc="46.4398, 5.5352";
$lat=46.4398; 
$lon=5.5352;
$cities=Array("Orgelet",  "Moutonne", "Rothonay", "Cressia", "Presilly", "Sarrogna", "Nancuise",
				"La Tour du Meix", "Ecrille", "Marnezia", "Merona", "La Chailleuse", "Plaisia", 
				"Pimorin", "Dompierre sur Mont", "Onoz", "Reithouse", "Chaveria", "Alieze", "Beffia", 
				"Augissey", "Chamberia", 
				"Arinthod", "Vosbles", "Aromas", "Villeneuve les Charnod", "Coisia", "Savigna", "Cezia", "Fétigny", "Cornod", 
				"Genod", "Valfin sur Valouse", "Dramelay", "Charnod", "Cernon", "Lavans sur Valouse", "Chemilla", 
				"Marigna sur Valouse", "Chisseria", "La Boissière", "Condes", "Chatonnay", "Legna", "Saint Hymetière", 
				"Thoirette", "Vescles");
				
sort($cities);
$geocities=Array();


if (!file_exists('geocities.dat')){
	foreach ($cities as $city){
		$apiurl='http://nominatim.openstreetmap.org/search?';

		$apiurl.='q='.urlencode($city);
		$apiurl.='&format=xml';
		$apiurl.='&polygon=1';
		$apiurl.='&addressdetails=1';
		$apiurl.='&email='.$lug_email;

		$lon=0;
		$lat=0;
		
		$apiresult=file_get_contents($apiurl);


		if ($apiresult){
				$dom = new DOMDocument();
			   $dom->loadXML($apiresult);
			   //$dom->preserveWhiteSpace=false;
			   $item = $dom;
			   
			   if ($item->getElementsByTagName('place')->item(0)!==null) {
				   //$item=$itemstop->item(0)->getElementsByTagName('addressparts')->item(0);
				   $lon = $item->getElementsByTagName('place')->item(0)->getAttribute('lon');
					$lat = $item->getElementsByTagName('place')->item(0)->getAttribute('lat');
					$geocities[$city]['lon']=$lon;
					$geocities[$city]['lat']=$lat;
				}
		}
		
	}
	
	file_put_contents('geocities.dat', serialize($geocities));
}	
else {
	$geocities=unserialize(file_get_contents('geocities.dat'));
}
$lat=0;
$lon=0;
$geocities['--sympathisant lointain--']['lat']=1000000000;
$geocities['--sympathisant lointain--']['lon']=1000000000;


if (isset($_GET['city'])){
			$geoloc=$geocities[$_GET['city']]['lat'].", ".$geocities[$_GET['city']]['lon'];
			$lat=$geocities[$_GET['city']]['lat'];
			$lon=$geocities[$_GET['city']]['lon'];
}			
			
$user=Array();
$sortedUsers=Array();
if (file_exists('./data/users.dat')){
			$user=unserialize(file_get_contents('./data/users.dat'));
			
			if (isset($_GET['city'])){
				$sortedUsers=Array();
				$keyuser=array_keys($user);
				foreach ($keyuser as $keyluser)
				{
					if (!isset($user[$keyluser]['lat'])){
								$user[$keyluser]['lat']=$geocities[$user[$keyluser]['city']]['lat'];
								$user[$keyluser]['lon']=$geocities[$user[$keyluser]['city']]['lon'];
								file_put_contents('./data/users.dat', serialize($user));
					}
					$arg1=0;
					$arg2=0;
					
					if ($user[$keyluser]['lat']>$lat)
						$arg1=$user[$keyluser]['lat']-$lat;
					else
						$arg1=$lat-$user[$keyluser]['lat'];
						
					if ($user[$keyluser]['lon']>$lat)
						$arg1=$user[$keyluser]['lon']-$lat;
					else
						$arg1=$lat-$user[$keyluser]['lon'];
						
						
						
					$sortedUsers[$keyluser]=sqrt($arg1*$arg1+$arg2*$arg2);
					
					
				}
			}
}				


asort ($sortedUsers);
$usertemp=Array();
if (isset($_GET['city'])){
	
			foreach (array_keys($sortedUsers) as $onezer){
				if (!$user[$onezer]['jailed'])
					array_push($usertemp, $user[$onezer]);
			
			}				
		$user=$usertemp;
}				



?><!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="favicon.png" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="charset" value="utf-8" />
<meta name="ICBM" value="<?php echo $geoloc;?>" />
<title>Groupe d'Utilisateurs Linux de la Petite Montagne - GUL Petite Montagne - <?php
if (isset($_GET['city'])){
			echo htmlspecialchars($_GET['city']);
}				


?>
</title>
<meta name="description" content="Groupe Linux d'entraide et de dépannage informatique en Petite Montagne du Jura" />
</head>
<body>
<?php 
if (!file_exists('./data/users.dat'))
	file_put_contents('./data/users.dat', serialize(Array()));

$lusers=unserialize(file_get_contents('./data/users.dat'));




if (isset($_GET['action'])&&$_GET['action']==='login'){
			$token='';
			
			$tokenz=Array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'a', 'z', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'm', 'l', 'k', 'j', 'h', 'g', 'f', 'd', 's', 'q', 'w', 'x', 'v', 'b', 'n',
			'A','Z','E','R','T','Y','U','I','O','P','Q','S','D','F','G','H','J','K','L','M','W','X','C','V','B','N');
			srand();
			for ($i=0;$i<12;$i++)
				$token.=$tokenz[mt_rand(0,count($tokenz)-1)];
				
			if (in_array($_POST['email'], array_keys($lusers))){	
				file_put_contents('./data/tokens/'.microtime(true), serialize(Array($token, $_POST['email']	)));
				if (mail($_POST['email'], 'Votre code de connexion gul.petite-montagne.net', $token, 'From: '.$lug_email)){
					
					
				
				
				}
				else
				{
				echo "ow, quelque chose d'anormal est arrivé et votre code de connexion n'a pas pû être envoyé par mail...";
				}
				
				
					
			}	
		echo "<div>Si votre adresse email est celle d'un compte utilisateur sur ce site, vous allez recevoir un code temporaire de connexion. Entrez-le dans la zone de saisie ci-dessous. </div>";
				echo '<form action="./?action=sso" method="post"><input type="hidden" name="email" value="'.htmlspecialchars($_POST['email']).'"/>Code : <input type="text" name="code" size="20"></input><input type="submit"/></form>';
				echo "vous n'avez pas reçu le code ?"; 
				
				echo '<a href="javascript:history.back(2)">Réessayer</a>';
				
				
				echo '</body></html>';			
		exit(0);
}				
if (isset($_GET['action'])&&$_GET['action']==='sso'){
			$okforme=false;
			$tokenz=array_diff(scandir('./data/tokens/'), Array('..', '.'));
			foreach ($tokenz as $token)
				if (unserialize(file_get_contents('./data/tokens/'.$token))[0]==$_POST['code']
				&&unserialize(file_get_contents('./data/tokens/'.$token))[1]==$_POST['email'])
					$okforme=true;
					
					if ($okforme){
						$_SESSION['loggued']=true;
						$lusers[$_POST['email']]['jailed']=false;
						
						if (!isset($lusers[$_POST['email']]['uid'])){
							$lusers[$_POST['email']]['uid']=microtime(true);
							file_put_contents('./data/users.dat', serialize($lusers));
							
							}
						
						
						$_SESSION['email']=$_POST['email'];
						$_SESSION['uid']=$lusers[$_POST['email']]['uid'];
						$_SESSION['name']=!isset($lusers[$_POST['email']]['name'])?'(pas de nom défini)':$lusers[$_POST['email']]['name'];
						$_SESSION['city']=$lusers[$_POST['email']]['city'];
						
						unlink('./data/tokens/'.$token);
						}
					else {
						echo 'Le code fourni est incorrect. <a href="javascript:history.back(3)">Réessayer</a></br></body></html>';
						exit (0);
						}
			}				

if (isset($_GET['action'])&&$_GET['action']==='register'){
			$lusers=unserialize(file_get_contents('./data/users.dat'));
			if (isset($lusers[$_POST['email']])){
					echo 'Impossible de créer un compte avec cet email. Ne souhaitiez-vous pas plutôt <a href="./#connect">vous connecter ?</a>';
					exit(0);}
			$lusers[$_POST['email']]=Array();
			$lusers[$_POST['email']]['city']=$_POST['city'];
			$lusers[$_POST['email']]['jailed']=true;
			file_put_contents('./data/users.dat', serialize($lusers));
			echo 'votre compte a été crée. Vous pouvez maintenant <a href="./#login">vous connecter</a></body></html>';
			exit(0);
			}


if (isset($_GET['logout'])&&$_GET['logout']==='logout'){
			unset($_SESSION['loggued']);
			echo 'Vous êtes à présent déconnecté. <a href="./">Retour'." à l'accueil</a></body></html>";
			exit (0);
		}


if(isset($_GET['action'])&&$_GET['action']=='message'&&isset($_GET['uid'])){
			if (!isset($_SESSION['loggued'])){
				echo '<em>vous devez être connecté pour utiliser cette fonctionnalité</em></body></html>';
				exit(0);
				}
			echo '<div>';
			echo '<h1>Envoyer un message</h1>';
			echo '<h3 style="display:inline;">Envoyer un message à : </h3>';
			echo '<form action="./?action=postMessage" method="post" style="display:inline;"><select required name="uid">';
			foreach ($lusers as $looozer){
				
				if (isset($looozer['uid'])&&!$looozer['jailed']) {
				
					echo '<option value="'.$looozer['uid'].'" ';
					
					if (strval($_GET['uid'])===strval($looozer['uid']))
						echo ' selected ';
						
					echo ' >';
					if (isset($looozer['name']))
						echo htmlspecialchars($looozer['name']);
					else echo '&lt;nom indéfini&gt;';
					echo ' ('.$looozer['city'].')';
					echo '</option>';
					}
				}
				echo '<option value="-1">Message global à tous les inscrits. Tout abus de cette fonction pourra conduire à des sanctions</option>';
				echo '</select><textarea rows="35" style="height:78%;width:100%;" name="message"></textarea>';
			echo '<input style="float:right;" type="submit"></form>';
			echo '<hr style="clear:both;"/>';
			 echo '</div>';
		}

if(isset($_GET['action'])&&$_GET['action']=='postMessage'&&isset($_POST['uid'])&&isset($_POST['message'])){
				
				$lusers=unserialize(file_get_contents('./data/users.dat'));

				
				if (!isset($_SESSION['loggued'])){
				echo '<em>vous devez être connecté pour utiliser cette fonctionnalité</em></body></html>';
				exit(0);
				}
				$messageSubject=$lug_name.' - Message';
					
				if ($_POST['uid']==-1)
					$messageSubject.=' Global';
					
				$messageSubject.=' de ';
					
				
				$messageSubject.=$_SESSION['name'].', '.$_SESSION['city'];
				
				
				
				$message='"'.$_POST['message'].'"';
				
				$message.='
				
				Répondez à ce message <a href="'.$server_address.'?action=message&uid='.urlencode($_SESSION['uid']).'">ici</a>.';
				
				
				$message = wordwrap($message, 70, "\r\n");

				$to='';
				
				
				if ($_POST['uid']!=-1){
					foreach ($lusers as $looozer){
						
						if (isset($looozer['uid'])&&!$looozer['jailed']) {
						
						
							if (strval($_POST['uid'])==strval($looozer['uid']))
										$to.=key($lusers);
								
							}
						}
					
					if(mail($to, $messageSubject, $message, 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=utf-8'."\r\n".'From: '.$lug_email)){
						echo 'Mail envoyé. <a href="./">Accueil</a></body></html>';
						exit(0);}
					}
					
					else{
						$counter=0;
					foreach ($lusers as $looozer){
						
						if (isset($looozer['uid'])&&!$looozer['jailed']) {
						
								if(mail(key($lusers), $messageSubject, $message, 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=utf-8'."\r\n".'From: '.$lug_email))
									$counter++;
									
									
									
										
								
							}
						}
					
						echo $counter.' mails envoyés. <a href="./">Accueil</a></body></html>';
						exit(0);
					}
					
					
					echo 'Il y a eu un problème pour envoyer le message';
					
					exit(0);
			}
?>
<a name="home"><em>Ce site n'utilise aucun cookie tiers dans aucun but que ce soit. Les seuls cookies utilisés par ce site sont simplement là pour maintenir les sessions des utilisateurs.</em></a>
<div style="float:right;">
	<?php if (isset($_SESSION['loggued']) && $_SESSION['loggued']){?>
	<a style="color:yellow;background-color:black">Vous êtes à présent connecté</a> <a href="./?profile=edit">Editer mon profil</a> ou 
	<a href="./?logout=logout">Me déconnecter</a>
	
	
	<?php } else {?>
	<a href="#login">Se connecter</a> ou <a href="#register">S'inscrire</a>
	
	
	
	<?php } ?>
	
	</div>
<hr style="clear:both;"/>
<h1 style="text-align:center;background-color:#090929;border-radius:14px;">
	<span style="text-decoration:underline;"><strong>G</strong></span>roupe d'<span style="text-decoration:underline;"><strong>U</strong></span>tilisateurs

	<span style="text-decoration:underline;"><em><strong>L</strong>inux</em></span>

	de la 
	
	
	<span style=""><strong>P</strong></span>etite
	<span style=""><strong>M</strong></span>ontagne
	du 
	<span style="text-decoration:underline;"><strong><em>Jura</em></strong></span>

</h1>
<h2 style="text-align:center;background-color:black;">gul.petite-montagne.net</h2><div style="text-align:center;border-radius:10px;background-color:#292522;">
<?php
if (!isset($_GET['city'])){ 

foreach ($cities as $city){ 
		$counter=0;
		foreach ($user as $dluzer)
			if ($dluzer['city']===$city)
				if(!$dluzer['jailed'])
					$counter++;
			echo '- <span><a href="./?city='.urlencode($city).'">'.htmlspecialchars($city).'</a>';
				
			if ($counter>0)	
				echo ' ('.$counter.') ';
			echo '</span> -';



		}
} else if (in_array($_GET['city'], $cities)){

		echo '<h3><em>Affichage des membres du GUL les plus proches de <strong>'.htmlspecialchars($_GET['city']).'</strong></em></h3><a href="./">Retour à l'."'".'accueil</a>';
}
?>

</div>
<hr/><?php if (!isset($_GET['city'])){
	
	?>
<span id="menu_bar">
<span class="menuItemSelected">-<a href="./#home"> accueil </a>-</span>
<span class="menuItem">-<a href="#why"> pourquoi Linux ? </a>-</span>
<!--<span class="menuItem">-<a href="#map"> la carte </a>-</span>-->
<span class="menuItem">-<a href="#members"> liste des membres </a>-</span>
<span class="menuItem">-nos <a target="new" href="http://nodni.clewn.org/?city=Valzin+en+petite+Montagne">évènements et manifestations</a> sur nodni-</span>
<span class="menuItem">-<a href="#irc"> Salon de discussion </a>-</span>
<span class="menuItem">-<a href="#recycle">Recyclage</a>-</span>

</span>
<hr/>
<div>
<a name="why"><h2>Pourquoi Linux...</h2></a>
<h3>Que faire avec un ordinateur qui rame ?</h3>
<h3>Comment réparer un ordinateur qui bloque ou qui ne démarre plus (écrans bleus) ?</h3>
<h3>Que faire quand un ordinateur devient trop vieux et n'est plus supporté par le vendeur du système d'exploitation ?</h3>
<h3>Que faire si mon ordinateur a des virus (ralentissement, apparition de pubs...)</h3>
<strong>Si vous avez déjà eu à vous poser l'une ou l'autre de ces questions</strong>, vous êtes arrivé au bon endroit. Un moyen simple de 
résoudre tous les problèmes de ce genre une fois pour toutes est de remplacer le système d'exploitation livré avec l'ordinateur, par Linux. 
<br/><br/>
<strong>Linux (qu'on appelle aussi <em>GNU/Linux</em>), c'est la stabilité, pas de virus, pas de ralentissement au fil du temps, des milliers de logiciels 
installables en un clic depuis les canaux de la logithèque, et surtout, <em>un environnement simple, logique et compréhensible qui est à la portée de ses utilisateurs</em> !
</strong>
<h3>Comment est-ce possible ?</h3>
GNU/Linux, contrairement à Windows et MacOS, n'est pas le produit d'un fabricant qui a pour but de vous vendre régulièrement une nouvelle version du système. 
<br/>
Linux est un <em><strong style="text-decoration:underline;">logiciel libre</strong></em> : ce sont ses utilisateurs qui développent et entretiennent le projet ; 
grâce à l'adoption de licences logicielles <em>libres</em> telles que, par exemple, la General Public License, les programmeurs s'assurent que leur travail restera
toujours un bien commun à la disposition des utilisateurs. 
<h3>Je voudrais passer à Linux, comment faire ?</h3>
Les Groupes d'Utilisateurs Linux sont un bon point de départ pour découvrir Linux en douceur. Contactez-donc l'un des membres du GUL de la Petite Montagne, 
dont vous trouverez la liste plus bas sur cette page. Installer Linux est très simple et rapide à faire et beaucoup de membres de GULs sont toujours ravis
 d'apporter leurs expertise et conseils pour mener à bien facilement ce processus. 
 </div>
<hr/>
<span id="menu_bar">
<span class="menuItem">-<a href="./#home"> accueil </a>-</span>
<span class="menuItemSelected">-<a href="#why"> pourquoi Linux ? </a>-</span>
<!--<span class="menuItem">-<a href="#map"> la carte </a>-</span>-->
<span class="menuItem">-<a href="#members"> liste des membres </a>-</span>
<span class="menuItem">-nos <a target="new" href="http://nodni.clewn.org/?city=Valzin+en+petite+Montagne">évènements et manifestations</a> sur nodni-</span>
<span class="menuItem">-<a href="#irc"> Salon de discussion </a>-</span>
<span class="menuItem">-<a href="#recycle">Recyclage</a>-</span>


</span>

<hr/>

<!--la carte-->

<?php
//liste des membres ; on affiche même si city est set
echo '<div><a name="members"><h2>Liste des membres</h2></a>';
}//if city is not set
else
	echo '<div>';
?>
<!---ici vient le code de ce qui est à afficher si city est set. Si city est set on trie par distance, sinon on trie par ancienneté -->

<?php
foreach ($user as $onezer){
				if (!$onezer['jailed']){
						echo '';
						echo '<hr/>';
						echo 'Membre numéro : '.$onezer['uid'];
						echo '<hr/>';
						
						if (isset($onezer['pic']))
							echo '<img style="float:left;width:10%;"  class="memberImg" src="./pic/'.$onezer['pic'].'" alt="Image de profil"/>';
						else
							echo '<img style="float:left;width:10%;" class="memberImg" src="./backgrrrnd.jpg" alt="Image de profil"/>';
						//echo 'Nom ou pseudonyme : ';
						//if (isset($onezer['name']))
						//	echo htmlspecialchars($onezer['name']);
						//else echo '<em>non renseigné</em>';
						echo '<br/>';
						echo 'Localité : ';
							echo htmlspecialchars($onezer['city']);
						echo '<br/>';
						//if (isset($onezer['desc']))
						//	echo '"<em>'.htmlspecialchars($onezer['desc']).'</em>"';
						//else echo "Ce membre n'a pas fourni de description";
						
						echo '<br/>';
						echo '<a style="float:right" href="./?action=message&uid='.urlencode($onezer['uid']).'">Envoyer un message à ce membre</a>';
						echo '<hr style="clear:both;"/>';
						echo '</div>';
				}
			}

?>
</div> <!--liste des membres-->





<?php if (!isset($_GET['city'])){
	
	?>
<hr/>
<span id="menu_bar">
<span class="menuItem">-<a href="./#home"> accueil </a>-</span>
<span class="menuItem">-<a href="#why"> pourquoi Linux ? </a>-</span>
<!--<span class="menuItem">-<a href="#map"> la carte </a>-</span>-->
<span class="menuItemSelected">-<a href="#members"> liste des membres </a>-</span>
<span class="menuItem">-nos <a target="new" href="http://nodni.clewn.org/?city=Valzin+en+petite+Montagne">évènements et manifestations</a> sur nodni-</span>
<span class="menuItem">-<a href="#irc"> Salon de discussion </a>-</span>
<span class="menuItem">-<a href="#recycle">Recyclage</a>-</span>


</span>

<hr/>	
	
<div>
<a name="irc"><h2>Salon de discussion</h2></a>
<iframe src="https://kiwiirc.com/client/irc.freenode.net/#gul-petite-montagne" style="border: 0; height: 450px; width: 100%;"></iframe>
</div>
<span id="menu_bar">
<span class="menuItem">-<a href="./#home"> accueil </a>-</span>
<span class="menuItem">-<a href="#why"> pourquoi Linux ? </a>-</span>
<!--<span class="menuItem">-<a href="#map"> la carte </a>-</span>-->
<span class="menuItem">-<a href="#members"> liste des membres </a>-</span>
<span class="menuItem">-nos <a target="new" href="http://nodni.clewn.org/?city=Valzin+en+petite+Montagne">évènements et manifestations</a> sur nodni-</span>
<span class="menuItemSelected">-<a href="#irc"> Salon de discussion </a>-</span>
<span class="menuItem">-<a href="#recycle">Recyclage</a>-</span>


</span>

<div>
<a name="recyle"><h2>Recyclage</h2></a>
<h2>Nous récupérons les vieux ordinateurs même hors service</h2>
<h3>Pour servir de source de pièces pour améliorer de vieilles machine ou en assembler une à partir de plusieurs autres</h3>
Laisser votre ancien ordinateur pour le recyclage c'est 
<ul>
<li>Un geste citoyen<br/>Vous permettez à des gens ayant peu de moyen de maintenir leur équipement numérique à jour</li>
<li>Un geste écologique<br/>Vous permettez à du matériel ancien de servir à nouveau, une fois remis en état par nos soins</li>
<li>Un geste sécurité<br/>Auquel on ne pense pas forcément quand on se débarrasse du matériel : notre GUL garantis l'effacement le plus définitif possible des supports de mémoire interne pour empêcher à 99% toute tentative de restauration de vos anciennes données personnelles. </li>
</ul>
<h3>N'hésitez pas à entrer en contact avec nos membres si vous avez du materiel qui vous encombre et que vous souhaitez donner.</h3>




</div>
<?php
}//if city is not set

if (!isset($_SESSION['loggued'])){
?>
<hr/><hr/><hr/><hr><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><div>
<a name="login"><h2>Se connecter - si vous avez déjà un compte</h2></a><form action="./?action=login" method="post">adresse mail : <input type="text" name="email" value="email@example.com"/><input type="submit"/></form>
</div><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><hr/><div>
<a name="register"><h2>S'inscrire - si vous souhaitez créer un compte sur le site	</h2></a><form action="./?action=register" method="post">adresse mail : <input type="text" name="email" value="email@example.com"/><br/>
Localité : 

<select name="city">
	<option selected value="--sympathisant lointain--">--Sympathisant lointain--</option>
<?php
foreach ($cities as $city)
		echo '<option value="'.htmlspecialchars($city).'">'.htmlspecialchars($city).'</option> ';
	

?>

</select>

<input type="submit"/>
</form>


</div>
<?php
}
?>
<hr/><hr/><hr/><hr/><hr/><h5 style="background-color:black;">
Ce site &copy; 2019 le GUL de la Petite Montagne - CNIL : N/A - background image : a.stafiniak [Attribution] - contact: <a href="mailto:gul@petite-montagne.net">gul@petite-montagne.net</a>. 
</h5>
<hr/><hr/><hr/><hr/><hr style="margin-bottom:200%;"/>
</div>
</body>
</html>
