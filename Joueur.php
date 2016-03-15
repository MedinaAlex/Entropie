<?php
	class Joueur {
		private $nom;
		private $pions;
		private $couleur;
		
		public function __construct($sonNom, $couleur) {
			$this->nom = $sonNom;
			$this->couleur = $couleur;
		}

		/** Permet de récuperer le nom du joueur
		* @return le nom du joueur
		*/
		public function getNom() {
			return $this->nom;
		}

		/** Permet de définir la liste des pions du joueur
		* @param $pions - liste de pions du joueur
		*/
		public function setPions($pions) {
			$this->pions = $pions;
		}

		/** Permet de récuperer la couleur du joueur
		* @return la couleur du joueur
		*/
		public function getCouleur() {
			return $this->couleur;
		}

		/** Permet de changer la couleur du joueur
		* @param $couleur - la couleur du joueur sous la forme d'une chaine de caractere
		*/
		public function setCouleur($couleur) {
			$this->couleur = $couleur;
		}
	}
?>
