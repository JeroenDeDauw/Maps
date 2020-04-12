<?php

declare( strict_types = 1 );

namespace Maps\Map\CargoFormat;

class PopupContent {

	private $titleHtml;
	private $propertyValues;

	/**
	 * @param string $titleHtml
	 * @param string[] $propertyValues
	 */
	public function __construct( string $titleHtml, array $propertyValues ) {
		$this->titleHtml = $titleHtml;
		$this->propertyValues = $propertyValues;
	}

	public function getHtml(): string {
		$valueList = $this->getPropertyValueList();
		$separator = $this->titleHtml === '' || $valueList === '' ? '' : '<br>';

		return '<h3>' . $this->titleHtml . '</h3>' . $separator . $valueList;
	}

	private function getPropertyValueList(): string {
		$lines = [];

		foreach ( $this->propertyValues as $name => $value ) {
			$lines[] = $this->bold( $this->stripTags( $name ) ) . ': ' . $this->stripTags( $value );
		}

		return implode( '<br>', $lines );
	}

	private function stripTags( string $html ): string {
		return strip_tags( $html, '<a><img>' );
	}

	private function bold( string $html ): string {
		return '<strong>' . $html . '</strong>';
	}

}
