<?php
	include 'Plateau.php';
	session_start();
	
	if(!isset($_GET['lastClic'])){
		$joueur1 = new Joueur('Alex', 'yellow');
		$joueur2 = new Joueur('Yanis', 'green');
		$plateau = new Plateau(5, $joueur1, $joueur2);

		$plateau->initPlateau();
		$_SESSION['joueur1'] = $joueur1;
		$_SESSION['joueur2'] = $joueur2;
		$_SESSION['plateau'] = $plateau;
	} 
	else {
		$plateau = $_SESSION['plateau'];
		$joueur1 = $_SESSION['joueur1'];
		$joueur2 = $_SESSION['joueur2'];		
	}

	/** Permet de récuperer les coordonnées X et Y d'une chaine sous la forme "X:Y"
	* @return un tableau de taille 2 avec [0] = X et [1] = Y
	*/
	function getCoo($strCoo) {
		return array($strCoo[0], $strCoo[2]);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Entropie</title>
	<style type="text/css">
		table {
			border: 1px solid black;
			border-collapse:collapse;
		}
		td {

			border: 1px solid black;
			width: 79px;
			height: 79px;
			vertical-align: top;
			padding: 0px;
			margin: 0px;
		}
		input.boutton {
			width: 100%;
			height: 100%;
			border: 0px;
			padding: 0px;
		}
		/* style du joueur 1 */
		/* Permet d'ajouter un rond sur le bouton pour représenter le pion de la bonne couleur*/
		input.boutton.<?php echo $joueur1->getNom();?> {
			background: <?php echo $joueur1->getCouleur(); ?>;
			border-radius:50%;
		}
		/* style du joueur 2 */
		/* Permet d'ajouter un rond sur le bouton pour représenter le pion de la bonne couleur*/
		input.boutton.<?php echo $joueur2->getNom();?> {
			background: <?php echo $joueur2->getCouleur(); ?>;
			border-radius:50%;
		}

		/* style des bouton annuler et suivant */
		input.choix {
			width: 200.5px;
			height: 40px;
			border: 1px solid;
		}

	</style>
</head>
<body>
	<center>
		<h1>Entropie</h1>
			<form action="application.php" method="get">

			<?php
				//permet de savoir si le plateau est affiché
				$afficher = false;
				// pion et cell sont des coordonnées de cellule sous forme "X:Y"

				// Si lastClic n'est pas définit dans l'url (c'est donc le début de la partie)
				if (!isset($_GET['lastClic'])) {
					//choisir les nom des joueurs, et leurs couleurs (peut etre fait en javasript ou passé dans l'url d'une de la page précédente)

					// On initialise les variables
					$_SESSION['pion'] = "";
					$_SESSION['cell'] = "";
				}
				// Sinon, si lastClic est définit (ce n'est pas le début de la partie)
				else {
					// si on vient d'annuler le coup précédent
					if ($_GET['lastClic'] === "annuler") {
						echo "<script>console.log('annulation');</script>";
						// On inverse les variable de sauvegarde avec les variable du dernier coup
						$tmpP = $_SESSION['pion'];
						$_SESSION['pion'] = $_SESSION['savePion'];
						$_SESSION['savePion'] = $tmpP;

						$tmpC = $_SESSION['cell'];
						$_SESSION['cell'] = $_SESSION['saveCell'];
						$_SESSION['saveCell'] = $tmpC;

						// Puis on déplace le pion sur ca cellule précédente
						$cooPion = getCoo($_SESSION['pion']);
						$celluleDepart = $plateau->getCellule($cooPion[0], $cooPion[1]);
							
						$cooCell = getCoo($_SESSION['cell']);
						$celluleArrive = $plateau->getCellule($cooCell[0], $cooCell[1]);

						$plateau->deplacer($celluleArrive, $celluleDepart);
						$_SESSION['pion'] = "";
						$_SESSION['cell'] = "";
					}
					// Sinon, si on vient de cliquer sur le bouton suivant
					else if ($_GET['lastClic'] === "suivant") {
						// On efface les valeurs du joueur précédent
						$_SESSION['pion'] = "";
						$_SESSION['cell'] = "";

						//on passe au tour suivant
						$plateau->tourSuivant();
						echo "<script>console.log('Au suivant !!');</script>";
					}
					// Sinon, si le joueur vient de cliquer sur une cellule
					else {
						// si le pion n'est pas définit
						if($_SESSION['pion'] === ""){
							$cooPion = getCoo($_GET['lastClic']);
							$cellule = $plateau->getCellule($cooPion[0], $cooPion[1]);
							$pion = $cellule->getContenu();

							//Si le Pion appartient au joueur courant
							if(get_class($pion) == "Pion" && $pion->getJoueur() == $plateau->getJoueurCourant()) {

								// alors on attribu à pion la valeur du dernier clic
								$_SESSION['pion'] = $_GET['lastClic'];
								echo "<script>console.log('pion = lastClic');</script>";
								//appel des déplacements possibles

								$cooPion = getCoo($_SESSION['pion']);
								$celluleDepart = $plateau->getCellule($cooPion[0], $cooPion[1]);

								if($plateau->getVoisinsJoueur($celluleDepart)){
									echo $plateau->afficher(true, $celluleDepart);
									$afficher = true;
								}
							}
						}
						// sinon, si pion est définit (donc cell n'est pas définit)
						else {
							// cell prend la valeur du dernier clic
							$_SESSION['cell'] = $_GET['lastClic'];
							echo "<script>console.log('cell = lastClic');</script>";

							// On sauvegarde les valeurs pion et cell
							$_SESSION['saveCell'] = $_SESSION['cell'];
							$_SESSION['savePion'] = $_SESSION['pion'];
							echo "<script>console.log('sauvegarde de cell et de pion');</script>";

							// On déplace le pion sur la bonne cellule
							$cooPion = getCoo($_SESSION['pion']);
							$celluleDepart = $plateau->getCellule($cooPion[0], $cooPion[1]);
							
							$cooCell = getCoo($_SESSION['cell']);
							$celluleArrive = $plateau->getCellule($cooCell[0], $cooCell[1]);

							//si le déplacement est possible
							if($plateau->deplacementPossible($celluleDepart, $celluleArrive)){
								$plateau->deplacer($celluleDepart, $celluleArrive);
							}

							echo "<script>console.log('déplacement(pion,cell)');</script>";

							// Puis on affiche annuler et suivant
							echo '<input class="choix" type="submit" name="lastClic" value="annuler">';
							echo '<input class="choix" type="submit" name="lastClic" value="suivant">';
						}
					}
				}


				//si le plateau n'est pas afficher
				if(!$afficher){
					//on l'affiche
					echo $plateau->afficher(false, null);
				}
				//On affiche le numéro de tour ainsi que le joueur qui est en train de jouer
				echo "Tour ".$plateau->getTour().". Au joueur ".$plateau->getJoueurCourant()->getNom() ." de jouer";

				//On regarde si le joueur courant à gagné
				if($plateau->gagne()){
					echo "<br><h1>gagne</h1>";
					echo header("location : http://localhost/miniProjet/gagne.php");
				}
			?>
			</form>
	</center>
</body>
</html>
