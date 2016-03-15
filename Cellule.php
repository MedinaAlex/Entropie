<?php
	//include 'Pion.php';
	class Cellule {
		private $posX;
		private $posY;
		private $contenu;
		
		/**Constructeur de la classe Cellule
		* @param $X la coordonnée X sur le plateau
		* @param $Y la coordonnée Y sur le plateau
		* @param $contenu Le contenu de la Cellule
		*/
		public function __construct($X, $Y, $contenu) {
			$this->posX = $X;
			$this->posY = $Y;
			$this->contenu = $contenu;
		}

		/** Permet de savoir si il y a un pion sur une cellule
		* @return false si un pion est sur la cellule, sinon true
		*/
		public function estVide() {
			return $this->contenu == null;
		}

		/** Permet de récuperer le contenu d'une cellule
		* @return le pion sur la cellule, sinon si il n'y en a pas, retourne null
		*/
		public function getContenu() {
			return $this->contenu;
		}

		/** Permet de placer un pion sur une cellule
		* @param $pion - le pion a placer sur la cellule
		*/
		public function setContenu(Pion $pion) {
			$this->contenu = $pion;
		}

		/** Permet de vider la cellule
		*/
		public function setContenuVide() {
			$this->contenu = null;
		}

		/** Permet de récuperer la position horizontale de la cellule
		* @return un entier entre 0 et la taille du plateau -1 compris 
		*/
		public function getX() {
			return $this->posX;
		}

		/** Permet de récuperer la position verticale de la cellule
		* @return un entier entre 0 et la taille du plateau -1 compris 
		*/
		public function getY() {
			return $this->posY;
		}

		/** Permet d'afficher la cellule et le pions si la cellule en contien un sous forme de bouton html.
		* @param $cliquable défini si le bouton sera cliquable
		* @return le code html ayant comme nom "lastClic" et comme valeur la position "X:Y" de la cellule
		*/
		public function afficher($cliquable) {
			$strId = '';
			if ($this->contenu != null) {
				$strId = $strId. ' '. $this->contenu->getJoueur()->getNom(); //ajoute une classe à la case pour afficher le pion
			}
			if($cliquable){
				return '<input class="boutton'.$strId.'" type="submit" name="lastClic" value="'. $this->__toString() .'">';
			}
			else{
				return '<input class="boutton'.$strId.'" type="submit" name="lastClic" disabled="disabled" value="'. $this->__toString() .'">';
			}
		}

		/** Retourne la représentation de la cellule sous la forme d'une chaine de caracter
		* @return retourne la cellule sous la forme "X:Y"
		*/
		public function __toString() {
			return $this->posX.":".$this->posY;
		}
	}
?>
