<?php

namespace PaymentPage\PostTypes\Form\FieldMap;

abstract class Skeleton {

	abstract public static function get_field_groups();

	public static function is_enabled() :bool {
		return true;
	}

}
