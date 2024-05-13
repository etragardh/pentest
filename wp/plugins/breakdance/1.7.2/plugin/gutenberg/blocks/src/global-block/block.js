/* global breakdanceGlobalBlock */
/**
 * BLOCK: Breakdance Global Block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import "./editor.scss";
import "./style.scss";

import Logo from "../logo";
import Sidebar from "./sidebar";
import BlockSSR from "./ssr";
import BlockChooser from "./chooser";

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { useCallback, useRef } = wp.element;

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType("breakdance/global-block", {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __("Breakdance Global Block"), // Block title.
	icon: Logo, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	description: "Add Breakdance Global Blocks to your Gutenberg Page",
	category: "common", // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	attributes: {
		blockId: {
			default: "",
			type: "string",
		},
	},
	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: (props) => {
		const { setAttributes, attributes } = props;
		const blockId = attributes.blockId;
		const blockPostTypeUrl = breakdanceGlobalBlock.blockPostTypeUrl;
		const iframe = useRef(null);

		const refreshIframe = useCallback(() => {
			const copyId = blockId;
			setBlockId(-1);

			setTimeout(() => {
				setBlockId(copyId);
				// iframe.current.contentWindow.location.reload();
			});
		}, [blockId]);

		const setBlockId = (id) => {
			setAttributes({ blockId: id });
		};

		const blockChooser = (
			<BlockChooser blockId={blockId} setBlockId={setBlockId} />
		);

		const sidebar = (
			<Sidebar blockId={blockId} onRefreshClick={refreshIframe}>
				{blockChooser}
			</Sidebar>
		);

		if (blockId) {
			return (
				<div>
					<BlockSSR blockId={blockId} iframeRef={iframe} />
					{sidebar}
				</div>
			);
		}

		return (
			<div className={props.className}>
				<p>
					Choose a Global Block from your library or{" "}
					<a href={blockPostTypeUrl} target="_blank" rel="noreferrer">
						create a new one
					</a>
					.
				</p>

				{blockChooser}
				{sidebar}
			</div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: (props) => {
		return null;
	},
});
