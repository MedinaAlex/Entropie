<?php
	class Pion {
		private $joueur;

		function __construct($joueur) {
			$this->joueur = $joueur;
		}

		/** Permet de récuperer le propriétaire du pion
		* @return Le joueur possedant le pion
		*/
		function getJoueur() {
			return $this->joueur;
		}
	}
?>
