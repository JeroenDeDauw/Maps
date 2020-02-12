<?php

namespace Maps\MediaWiki\Content;

use FormatJson;
use Maps\Presentation\GeoJsonMapPageUi;
use Maps\Presentation\OutputFacade;
use ParserOptions;
use ParserOutput;
use SMW\ApplicationFactory;
use SMW\DIProperty;
use Status;
use Title;

class GeoJsonContent extends \JsonContent {

	public const CONTENT_MODEL_ID = 'GeoJSON';

	public static function newEmptyContentString(): string {
		$text = '{"type": "FeatureCollection", "features": []}';
		return FormatJson::encode( FormatJson::parse( $text )->getValue(), true, FormatJson::UTF8_OK );
	}

	public function __construct( string $text, string $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct(
			$text,
			$modelId
		);
	}

	public function getData(): Status {
		$status = parent::getData();

		if ( $status->isGood() && !$this->isGeoJson( $status->getValue() ) ) {
			return Status::newFatal( 'Invalid GeoJson' );
		}

		return $status;
	}

	private function isGeoJson( $json ): bool {
		return property_exists( $json, 'type' )
			&& $json->type === 'FeatureCollection'
			&& property_exists( $json, 'features' )
			&& is_array( $json->features );
	}

	protected function fillParserOutput( Title $title, $revId, ParserOptions $options,
		$generateHtml, ParserOutput &$output ) {

		if ( !$generateHtml || !$this->isValid() ) {
			$output->setText( '' );
			return;
		}

		$this->addMapHtmlToOutput( $output );

		$this->todoStoreSomeSmwStuff( $title, $output );
	}

	private function addMapHtmlToOutput( ParserOutput $output ) {
		( GeoJsonMapPageUi::forExistingPage( $this->beautifyJSON() ) )->addToOutput( OutputFacade::newFromParserOutput( $output ) );
	}

	// TODO
	private function todoStoreSomeSmwStuff( Title $title, ParserOutput $output ) {
		$parserData = ApplicationFactory::getInstance()->newParserData( $title, $output );

		$parserData->getSemanticData()->addPropertyObjectValue(
			new DIProperty( 'HasNumber' ),
			new \SMWDINumber( 42 )
		);

		$parserData->copyToParserOutput();
	}

}
