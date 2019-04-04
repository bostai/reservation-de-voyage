<?php 
	$IdV = $_GET['voyage'];
	$rep = mysqli_query($connect,"SELECT *, COUNT(IdTicket) as NbP 
		FROM voyage
		INNER JOIN periode ON periode.IdPeriode = voyage.Periode
		INNER JOIN agence ON agence.IdAgence = periode.Agence
		INNER JOIN chauffeur ON chauffeur.IdChauffeur = voyage.Chauffeur
		INNER JOIN vehicule ON vehicule.IdVehicule = voyage.Vehicule
		INNER JOIN localite ON localite.IdLocalite = voyage.Localite
		LEFT JOIN ticket ON ticket.Voyage = voyage.IdVoyage
		
		WHERE IdVoyage = '$IdV'
		
		GROUP BY IdVoyage
		
		 ");

	$tab = mysqli_fetch_array($rep, MYSQLI_ASSOC) ;
?> 

<h1>RESERVER VOTRE PLACE POUR LE VOYAGE</h1>

<?php 

//Insertion d'un enregistrement dans la BD	
	if (isset($_POST['Voyageur']) && ($_POST['Voyageur']!=NULL))
	{
		if ((mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM ticket WHERE VoyageurTicket like '".$_POST['Voyageur']."' "), MYSQLI_ASSOC)))
		{
			
			include("config/messagePresence.php");
		}
		else
		{
			$Code = "v".$tab['IdVoyage'].".".dateAbregeeAnglaise(time()).".".ChampAutoIncrement('ticket', 'IdTicket');
			$Voyageur = mysqli_real_escape_string($connect,htmlspecialchars(addslashes($_POST['Voyageur'])));
			$Coordonnees = mysqli_real_escape_string($connect,htmlspecialchars(addslashes($_POST['Coordonnees'])));
			$Observation = mysqli_real_escape_string($connect,htmlspecialchars(addslashes($_POST['Observation'])));
			
			mysqli_query($connect,"INSERT INTO ticket VALUES('0','$IdV','$Code','$Voyageur','$Coordonnees','','$Observation', 'RESERVE', '', '".time()."','AFFICHER')");
				
			if (isset($_SESSION['IdU']))
			{
				$u = $_SESSION['IdU'];
			}
			else
			{
				$u = 0;
			}
			insererMouchard ('a effectué une Réservation pour '.$Voyageur, $u);
			include("config/messageInsertion.php");
			echo "Votre reservation a été prise en compte, vous êtes priés de vous présenter à l'agence de Voyage au plutard le ".nl2br(stripslashes(date('j M Y'))).", 30 minutes avant l'heure de Départ pour régler votre facture au guichet.<hr />POUR FAIRE UNE AUTRE RESERVATION AU MEME VOYAGE, REMPLISSEZ JUSTE LE MEME FORMULAIRE EN DESSOUS<hr />" ;
			
		}
	}	
?>



<table width="100%" border="0">
  <tbody>
    <tr>
      <td><u>AGENCE DE DEPART :</u><?php echo nl2br(stripslashes($tab['LibelleAgence']));?></td>
      <td><u>DATE ET HEURE DE DEPART :</u><?php echo nl2br(stripslashes(date('j M Y à H:i',$tab['DateVoyage'])));?></td>
    </tr>
    <tr>
      <td><u>DESTINATION :</u><?php echo nl2br(stripslashes($tab['LibelleLocalite']));?></td>
      <td><u>COÛT DU VOYAGE :</u><?php echo nl2br(stripslashes($tab['PrixVoyage']));?> FCFA</td>
    </tr>
    <tr>
      <td><u>VEHICULE :</u><?php echo nl2br(stripslashes(strtoupper($tab['LibelleVehicule'])));?></td>
      <td><u>CHAUFFEUR :</u> <?php echo nl2br(stripslashes($tab['NomChauffeur']." ".$tab['PrenomChauffeur']));?></td>
    </tr>
    <tr>
      <td><?php echo nl2br(stripslashes(strtoupper($tab['PlacesVehicule'])));?> places au Total</td>
      <td><?php echo nl2br(stripslashes(strtoupper($tab['PlacesVehicule']-$tab['NbP'])));?> places encore disponibles</td>
    </tr>
    <tr>
      <td><?php
                $repi = mysqli_query($connect,"SELECT * 
                    FROM photo 
                    WHERE ObjetPhoto LIKE 'VEHICULE' 
                    AND IdObjetPhoto LIKE '".$tab['IdVehicule']."' ");
                if ($tabi = mysqli_fetch_array($repi, MYSQLI_ASSOC))
                {
                    $image=$tabi['ImagePhoto'];
                    $Idimg = $tabi['ObjetPhoto'].$tabi['IdObjetPhoto'].$tabi['IdPhoto'];
                    $fichier=fopen("img_tmp/".$Idimg.".jpg","w");
                    fwrite($fichier,$image);
                    fclose($fichier);
                    
                    echo "<input type=\"image\" height=\"200\" class=\"button3\" src=\"img_tmp/".$Idimg.".jpg\" />";
                }
				else
				
            ?></td>
      <td><?php
                $repi = mysqli_query($connect,"SELECT * 
                    FROM photo 
                    WHERE ObjetPhoto LIKE 'CHAUFFEUR' 
                    AND IdObjetPhoto LIKE '".$tab['IdChauffeur']."' ");
                if ($tabi = mysqli_fetch_array($repi, MYSQLI_ASSOC))
                {
                    $image=$tabi['ImagePhoto'];
                    $Idimg = $tabi['ObjetPhoto'].$tabi['IdObjetPhoto'].$tabi['IdPhoto'];
                    $fichier=fopen("img_tmp/".$Idimg.".jpg","w");
                    fwrite($fichier,$image);
                    fclose($fichier);
                    
                    echo "<input type=\"image\" height=\"200\" class=\"button3\" src=\"img_tmp/".$Idimg.".jpg\" />";
                }
				else
				
            ?></td>
    </tr>
  </tbody>
</table>

<form action="index.php?p=ticket&voyage=<?php echo $IdV;?>" method="post" enctype="multipart/form-data" name="ticket">
<h3>Remplissez le formulaire pour une reservation</h3>
<table width="100%">
  
  <tr>
    <td>Nom du Voyageur</td>
    <td><input name="Voyageur" type="text" id="Voyageur" value="" onClick="document.refresh()" /></td>
    <td rowspan="4"><a href="index.php" class="button2">FERMER</a></td>
  </tr>
   <tr>
    <td>Coordonnées (Tel, Email)</td>
    <td><input name="Coordonnees" type="text" id="Coordonnees" value="" /></td>
    </tr>
  <tr>
    <td>Remarque</td>
    <td><input name="Observation" type="text" id="Observation" value="" /></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Valider" id="Valider" value="Valider">
      <input type="reset" name="Annuler" id="Annuler" value="Annuler"></td>
    </tr>
</table>

</form>
