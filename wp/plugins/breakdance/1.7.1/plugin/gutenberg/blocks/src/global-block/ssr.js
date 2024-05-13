/* global breakdanceGlobalBlock */
const { useState, useEffect } = wp.element;
const { Spinner } = wp.components;

export default function BlockSSR( props ) {
	const { blockPreviewUrl } = breakdanceGlobalBlock;
	const iframeUrl = blockPreviewUrl.replace( '%%BLOCKID%%', props.blockId );

	const [ isLoading, setIsLoading ] = useState( true );
	const [ isEmpty, setIsEmpty ] = useState( false );
	const [ iframeHeight, setIframeHeight ] = useState( null );

	useEffect( () => {
		setIsLoading( true );
		setIsEmpty( false );
	}, [ props.blockId ] );

	const onLoad = ( event ) => {
		const iframeDocument = event.target.contentDocument.documentElement;
		const height = iframeDocument.scrollHeight;

		// Check if a .section-container exists, otherwise show an "empty" message.
		const hasChildren = iframeDocument.querySelector( '.section-container' );

		setIframeHeight( height + 'px' );
		setIsLoading( false );
		setIsEmpty( ! hasChildren );
	};

	const emptyContent = (
		<div className="breakdance-global-block-placeholder">
			The current block is empty.
		</div>
	);

	const loader = (
		<div className="breakdance-global-block-placeholder">
			Loading block
			<Spinner />
		</div>
	);

	let classes = 'breakdance-global-block-ssr';

	if ( isLoading ) {
		classes += ' breakdance-global-block-ssr--loading';
	}

	return (
		<div className={ classes }>
			{ isLoading ? loader : null }

			{ isEmpty ? emptyContent : (
				<iframe
					title="Global Block"
					className="breakdance-global-block-iframe"
					src={ iframeUrl }
					style={ { height: iframeHeight } }
					ref={ props.iframeRef }
					onLoad={ onLoad }
				/>
			) }
		</div>
	);
}
