<?php
			$Temp = new Template;
			$Temp->dir = $logged['dskin'];
			$Temp->file = "footer.tpl";
			$Temp->tp();
			echo $Temp->html;
?>