<?php

class RM_Deactivator_Addon {

	public static function deactivate() {
            do_action("registrationmagic_addon_deactivated");
	}

}