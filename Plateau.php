<?php
	include 'Cellule.php';
	include 'Joueur.php';
	include 'Pion.php';

	class Plateau {
		private $taille;
		private $joueur1;
		private $joueur2;
		private $matrice;
		private $matrice_deplacement;
		private $tour;

		public function __construct($taille, $joueur1, $joueur2) {
			$this->taille = $taille;
			$this->joueur1 = $joueur1;
			$this->joueur2 = $joueur2;
			$this->matrice = array(array());
			$this->tour = 1;
			echo "<script>console.log('init');</script>";
		}

		/** Permet d'initialiser le plateau et de placer les pions
		*/
		public function initPlateau() {
			for ($i = 0; $i < $this->taille; $i++) { // parcours des y
				for ($j=0; $j < $this->taille; $j++) { // parcours des x
					if(($i == 0) || (($i == 1) && (($j==0) || ($j==4)))) { //sur les 7 cases de départ du haut du plateau (joueur1)
						$this->matrice[$i][$j] = new Cellule($j,$i, new Pion($this->joueur1));
					} else if (($i == 4) || (($i == 3) && (($j==0) || ($j==4)))) { // sur les 7 cases de départ du bas du plateau (joueur2)
						$this->matrice[$i][$j] = new Cellule($j,$i, new Pion($this->joueur2));
					} else {
						$this->matrice[$i][$j] = new Cellule($j,$i,null);
					}
				}
			}
		}

		/** affiche le plateau sous la forme d'un tableau html
		* @param $type boolean permet de savoir si l'on teste les arrivezs possible
		* @param $cellule la Cellule dont on veux savoir les arrivees possible
		* @return le code html sous forme d'une chaine de caractere
		*/
		public function afficher($type, $cellule) {
			$str = "<table>";
			if(get_class($cellule) == "Cellule"){
				$cellulePossible = $this->arriveePossible($cellule);
			}
			
			for ($i=0; $i < $this->taille; $i++) {  // table row (y)
				$str = $str. "<tr>";
				for ($j=0; $j < $this->taille; $j++) {  // table data (x)
					if(get_class($cellule) == "Cellule") {
						echo in_array($this->getCellule($j,$i), $cellulePossible);
					}
					
					if(!$type){
						$str = $str. '<td>'. $this->getCellule($j,$i)->afficher(true) .'</td>';
					}
					else if(get_class($cellule) == "Cellule" && in_array($this->getCellule($j,$i), $cellulePossible)){
						$str = $str. '<td>'. $this->getCellule($j,$i)->afficher(true) .'</td>';
					}
					else{
						$str = $str. '<td>'. $this->getCellule($j,$i)->afficher(false) .'</td>';
					}
				}
				$str = $str. "</tr>";
			}
			return $str . "</table>";
		}

		/** Permet de récuperer une cellule selon sa position
		* @param $x la coordonee x du plateau
		* @param $y la coordonee y du plateau
		* @return la cellule correspondant à la position $x:$y, ou null si la position est hors du plateau
		*/
		public function getCellule($x, $y) {
			if(($x < 0) || ($x >= $this->taille) || ($y < 0) || $y >= ($this->taille)) {
				return null;
			}
			return $this->matrice[$y][$x];
		}

		/** Permet de déplacer un pion sur une cellule
		* @param $celluleDepart - la cellule de départ 
		* @param $celluleArrive - la cellule d'arrivée
		*/
		public function deplacer(Cellule $celluleDepart, Cellule $celluleArrive) {
			if(!$celluleDepart->estVide()){
				$pion = $celluleDepart->getContenu();
			}
			else {
				$pion = $celluleArrive->getContenu();
			}

			if(get_class($pion) == "Pion"){
				$celluleArrive->setContenu($pion);
				$celluleDepart->setContenuVide();
			}
			else{
				$celluleDepart->setContenu($pion);
				$celluleArrive->setContenuVide();
			}
		}

		/** Permet de savoir si la partie est finie et retourne le gagnant
		* @return le joueur qui à gagné, ou si la partie n'est pas finie, retounre null
		*/
		public function gagne() {
			$gagne = true;
			for ($i = 0; $i < $this->taille; $i++) { // parcours des y
				for ($j=0; $j < $this->taille; $j++) { // parcours des x
					if($this->matrice[$j][$i]->getContenu() != null && $this->matrice[$j][$i]->getContenu()->getJoueur() == $this->getJoueurCourant()
					&& sizeof($this->getVoisinsJoueur($this->matrice[$j][$i]))>0 ){
						$gagne = false;
					}
				}
			}
			return $gagne;
		}

		/** retourne les voisins d'une cellule
		* @param la cellule en question
		* @return un tableau de taille 8 avec les voisins correspondant respectivement selon l'ordre du tableau à:
		*  [N, NE, E, SE, S, SO, O, NO]
		*/
		public function getVoisins(Cellule $c) {
			$voisins = array();
			$x = $c->getX();
			$y = $c->getY();

			$voisins[0] = $this->getCellule($x, $y-1); 		// N
			$voisins[1] = $this->getCellule($x+1, $y-1); 	// NE
			$voisins[2] = $this->getCellule($x+1, $y); 		// E
			$voisins[3] = $this->getCellule($x+1, $y+1); 	// SE
			$voisins[4] = $this->getCellule($x, $y+1);		// S
			$voisins[5] = $this->getCellule($x-1, $y+1);	// SO
			$voisins[6] = $this->getCellule($x-1, $y);		// O
			$voisins[7] = $this->getCellule($x-1, $y-1);	// NO

			return $voisins;
		}

		/** retourne les cellules avec un pion du même joueur d'une cellule dans son voisinage
		* @param la cellule en question
		* @return un tableau de taille 8 avec les voisins correspondant respectivement selon l'ordre du tableau à:
		*  [N, NE, E, SE, S, SO, O, NO]
		*/
		public function getVoisinsJoueur(Cellule $c){
			$voisins = $this->getVoisins($c);
			$taille =8;

			for ($i=0; $i < 8; $i++) {
				if($voisins[$i] == null){
					unset($voisins[$i]);
					$taille--;
				}
			}
			$voisins = array_values($voisins);

			for ($i=0; $i < $taille; $i++) { 
				if($voisins[$i]->estVide() || $voisins[$i]->getContenu()->getJoueur() != $this->getJoueurCourant() ) {
					unset($voisins[$i]);			
				}	
			}
			return array_values($voisins);
		}

		/** récupère la Cellule d'un Pion
		* @param le pion que l'on cherche
		* @return la cellule du pion*/
		public function getCellulePion(Pion $pion){
			for ($i = 0; $i < $this->taille; $i++) { // parcours des y
				for ($j=0; $j < $this->taille; $j++) { // parcours des x
					if($this->matrice[$i][$j]->getContenu() == $pion){
						return $this->matrice[$i][$j];
					}
				}
			}
		}

		/** passe au tour suivant
		*/
		public function tourSuivant(){
			$this->tour++;
		}

		/** renvoie le numéro de tour
		* @return le numéro de tour
		*/
		public function getTour(){
			return $this->tour;
		}

		/** renvoie le Joueur courant
		* @return le joueur courant
		*/
		public function getJoueurCourant(){
			if($this->getTour() % 2 -1== 0){
				return $this->joueur1;
			}
			else {
				return $this->joueur2;
			}
		}

		/** permet de savoir si un pion peut être déplacé 
		* @param $celluleDepart la cellule où se trouve le pion
		* @return true si c'est possible, false sinon
		*/
		public function deplacementPossible(Cellule $celluleDepart){
			$deplacement = false;
			$pion = $celluleDepart->getContenu();
			$voisins = $this->getVoisins($celluleDepart);

			if(get_class($pion) == "Pion" && $pion->getJoueur() == $this->getJoueurCourant()){
				$voisins = $this->getVoisinsJoueur($celluleDepart);
				
				//si un des voisins est un Pion du même joueur
				if(sizeof($voisins)>=1){
					$deplacement = true;
				}
			}
			return $deplacement;
		}

		/** teste les différentes arrivées possible
		* @param $celluleDepart la cellule de depart
		* @return un tableau avec les cellules d'arrivées possible
		*/
		public function arriveePossible(Cellule $celluleDepart){
			$array = array();
			for ($i=0; $i <= 7; $i++) {
				$voisins = $this->getVoisins($celluleDepart);
				$celluleCourante = $voisins[$i];

				while(get_class($celluleCourante) == "Cellule" && $celluleCourante->estVide()){
				 	$array[] = $celluleCourante;
				 	$voisins = $this->getVoisins($celluleCourante);
				 	$celluleCourante = $voisins[$i];
				}
			} 
			return $array;
		}
	}
?>