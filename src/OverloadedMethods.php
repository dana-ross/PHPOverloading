<?php

namespace DaveRoss\PHPOverloading;

trait OverloadedMethods {

	public function __construct() {

		if ( 0 === func_num_args() ) {
			// Default constructor
			call_user_func( array( __CLASS__, '__construct_default' ) );
		} else {
			// Overloaded constructor
			$fn = self::_overridden_impl_fn_name( '__construct', func_get_args() );
			if ( false !== $fn ) {
				call_user_func_array( array(
					__CLASS__,
					$fn
				), func_get_args() );
			} else {
				throw new \RuntimeException( 'No applicable constructor for ' . self::_overridden_impl_fn_name( '__construct', func_get_args() ) . ' found in ' . __CLASS__ );
			}
		}

	}

	/**
	 * @param string $name
	 * @param array  $args
	 * @return mixed
	 *
	 * @todo iterate through argument class names with increasing genericness to allow passing a subclass to a method expecting its parent
	 */
	public function __call( $name, array $args ) {

		$fn = self::_overridden_impl_fn_name( $name, $args );
		if ( false !== $fn ) {
			return call_user_func_array( array(
				__CLASS__,
				$fn
			), $args );
		} else {
			throw new \RuntimeException( 'No applicable implementation for ' . self::_overridden_impl_fn_name( $name, $args ) . ' found in ' . __CLASS__ );
		}

	}

	public static function _overridden_impl_fn_name( $called_fn, $args ) {

		// Try the direct mapping
		$fn = $called_fn . '_' . implode( '_', array_map( 'strtolower', array_map( array(
				__CLASS__,
				'_overridden_impl_fn_part'
			), $args ) ) );

		if ( method_exists( __CLASS__, $fn ) ) {
			return $fn;
		}

		$arg_types = self::_overridden_impl_arg_types( $args );
		$arg_types = array_map( function ( $index, $arg ) use ( $args, $arg_types ) {
			if ( ! is_scalar( $arg ) && ! is_array( $arg ) && false !== get_class( $arg ) ) {
				$reflection     = new \ReflectionClass( $args[ $index ] );
				$this_arg_types = array( $reflection->getShortName() );
				while ( $parent_class = $reflection->getParentClass() ) {
					$this_arg_types[] = $parent_class->getShortName();
					$reflection       = $parent_class;
				}

				return $this_arg_types;
			} else {
				return $arg_types[ $index ];
			}

		}, range( 0, count( $args ) - 1 ), $args );

		$fn = self::_find_callable_for_arg_types( $called_fn, $arg_types );
		if ( ! empty( $fn ) ) {
			return $fn;
		}

		// Fallthrough
		return false;
	}

	public static function _find_callable_for_arg_types( $called_function, array $remaining_arg_types, array $current_arg_types = array() ) {
		if ( count( $remaining_arg_types ) ) {
			$current_arg = array_shift( $remaining_arg_types );
			if ( is_array( $current_arg ) ) {
				foreach ( $current_arg as $index => $current_arg_type ) {
					$fn = self::_find_callable_for_arg_types( $called_function, $remaining_arg_types, array_merge( $current_arg_types, array( $current_arg_type ) ) );
					if ( $fn ) {
						return $fn;
					}
				}
			} else {
				$fn = self::_find_callable_for_arg_types( $called_function, $remaining_arg_types, array_merge( $current_arg_types, array( $current_arg ) ) );
				if ( $fn ) {
					return $fn;
				}
			}
		} else {
			$method_name = $called_function . '_' . implode( '_', array_map( 'strtolower', $current_arg_types ) );
			if ( method_exists( __CLASS__, $method_name ) ) {
				return $method_name;
			} else {
				return false;
			}
		}
	}

	public static function _overridden_impl_fn_part( $arg ) {
		return strtolower( is_object( $arg ) ? ( new \ReflectionClass( $arg ) )->getShortName() : gettype( $arg ) );
	}

	public static function _overridden_impl_arg_types( $args ) {
		return array_map( function ( $arg ) {
			return is_object( $arg ) ? ( new \ReflectionClass( $arg ) )->name : gettype( $arg );
		}, $args );
	}

}