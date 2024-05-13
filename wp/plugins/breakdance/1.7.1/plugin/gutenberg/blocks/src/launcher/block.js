/* global breakdanceConfig, breakdanceUtils */
/**
 * BLOCK: breakdance-launcher
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import "./editor.scss";
import "./style.scss";

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { useEffect } = wp.element;
import { useDispatch } from "@wordpress/data";
import Logo from "../logo";

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
registerBlockType("breakdance/block-breakdance-launcher", {
	title: __("Breakdance Launcher"),
	icon: Logo,
	category: "common",
	supports: {
		multiple: false,
		html: false,
		customClassName: false,
		reusable: false,
		inserter: false,
	},
	edit: (props) => {
		const { strings } = breakdanceConfig;
		const { removeBlock } = useDispatch("core/block-editor");

		useEffect(() => {
			breakdanceUtils.enableGutenbergReadOnlyModeIfLauncherIsPresent();
		}, []);

		const editWithBreakdance = (event) => {
			const newTab = breakdanceUtils.isAuxClick(event);

			breakdanceUtils.autogenerateTitleIfNotSet().saveGutenberg(() => {
				breakdanceUtils.redirectToBuilder(newTab);
			});
		};

		const remove = () => {
			breakdanceUtils.disableGutenbergReadOnlyMode();
			removeBlock(props.clientId);
		};

		const disableBreakdance = () => {
			if (breakdanceConfig.mode === "wordpress") {
				return remove();
			}

			breakdanceUtils.disableAndExtractContent(remove);
		};

		return (
			<div className={props.className}>
				<div className="breakdance-launcher">
					<p className="breakdance-launcher__description">
						{strings.description}
					</p>

					<div className="breakdance-launcher__buttons">
						<button
							className="breakdance-launcher-button"
							data-test-id="launcher-edit"
							type="button"
							onClick={editWithBreakdance}
						>
							{strings.openButton}
						</button>
						{breakdanceConfig.canUseDefaultEditor ? (
							<button
								className="breakdance-launcher-link"
								data-test-id="launcher-disable"
								type="button"
								onClick={disableBreakdance}
							>
								{strings.disableButton}
							</button>
						) : null}
					</div>
				</div>
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
