<?php
/**
 * CssMin - A (simple) css minifier with benefits
 * 
 * --
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * --
 *
 * @package		CssMin
 * @link		http://code.google.com/p/cssmin/
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2011 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		2.0.2.2 (2011-03-28)
 */
class CssMin
	{
	/**
	 * Null-Token.
	 * 
	 * @var integer
	 */
	const T_NULL = 0;
	/**
	 * State: Is in document
	 * 
	 * @var integer
	 */
	const T_DOCUMENT = 1;
	/**
	 * Token: Comment
	 * 
	 * @var integer
	 */
	const T_COMMENT = 2;
	/**
	 * Token: Generic at-rule
	 * 
	 * @var integer
	 */
	const T_AT_RULE = 3;
	/**
	 * Token: Generic at-rule
	 * 
	 * @var integer
	 */
	const T_AT_IMPORT = 22;
	/**
	 * Token: Start of @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA_START = 4;
	/**
	 * State: Is in @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA = 5;
	/**
	 * Token: End of @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA_END = 6;
	/**
	 * Token: Start of @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE_START = 7;
	/**
	 * State: Is in @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE = 8;
	/**
	 * Token: @font-face declaration
	 * 
	 * @var integer
	 */
	const T_FONT_FACE_DECLARATION = 9;
	/**
	 * Token: End of @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE_END = 10;
	/**
	 * Token: Start of @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE_START = 11;
	/**
	 * State: Is in @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE = 12;
	/**
	 * Token: @page declaration
	 * 
	 * @var integer
	 */
	const T_PAGE_DECLARATION = 13;
	/**
	 * Token: End of @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE_END = 14;
	/**
	 * Token: Start of ruleset
	 * 
	 * @var integer
	 */
	const T_RULESET_START = 15;
	/**
	 * Token: Ruleset selectors
	 * 
	 * @var integer
	 */
	const T_SELECTORS = 16;
	/**
	 * Token: Start of declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS_START = 17;
	/**
	 * State: Is in declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS = 18;
	/**
	 * Token: Declaration
	 * 
	 * @var integer
	 */
	const T_DECLARATION = 19;
	/**
	 * Token: End of declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS_END = 20;
	/**
	 * Token: End of ruleset
	 * 
	 * @var integer
	 */
	const T_RULESET_END = 21;
	/**
	 * Token: Start of @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES_START = 100;
	/**
	 * State: Is in @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES = 101;
	/**
	 * Token: @variables declaration
	 * 
	 * @var integer
	 */
	const T_VARIABLE_DECLARATION = 102;
	/**
	 * Token: End of @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES_END = 103;
	/**
	 * State: Is in string
	 * 
	 * @var integer
	 */
	const T_STRING = 253;
	/**
	 * State: Is in url string property
	 * 
	 * @var integer
	 */
	const T_STRING_URL = 254;
	/**
	 * State: Is in expression string property
	 * 
	 * @var integer
	 */
	const T_STRING_EXPRESSION = 255;
	/**
	 * Default configuration.
	 * 
	 * @var array
	 */
	private static $defaultConfiguration = array
		(
		"remove-empty-blocks"			=> true,
		"remove-empty-rulesets"			=> true,
		"remove-last-semicolons"		=> true,
		"remove-comments"				=> true,
		"convert-css3-properties"		=> false,
		"convert-font-weight-values"	=> false,
		"convert-named-color-values"	=> false,
		"convert-hsl-color-values"		=> false,
		"convert-rgb-color-values"		=> false,
		"compress-color-values"			=> false,
		"compress-unit-values"			=> false,
		"emulate-css3-variables"		=> true,
		"import-imports"				=> false,
		"import-base-path"				=> null,
		"import-remove-invalid"			=> false
		);
	/**
	 * Css color transformations table. Used to convert named colors to hexadecimal notation.
	 * 
	 * @var array
	 */
	private static $colorTransformations = array
		(
		"aliceblue"						=> "#f0f8ff",
		"antiquewhite"					=> "#faebd7",
		"aqua"							=> "#0ff",
		"aquamarine"					=> "#7fffd4",
		"azure"							=> "#f0ffff",
		"beige"							=> "#f5f5dc",
		"black"							=> "#000",
		"blue"							=> "#00f",
		"blueviolet"					=> "#8a2be2",
		"brown"							=> "#a52a2a",
		"burlywood"						=> "#deb887",
		"cadetblue"						=> "#5f9ea0",
		"chartreuse"					=> "#7fff00",
		"chocolate"						=> "#d2691e",
		"coral"							=> "#ff7f50",
		"cornflowerblue"				=> "#6495ed",
		"cornsilk"						=> "#fff8dc",
		"crimson"						=> "#dc143c",
		"darkblue"						=> "#00008b",
		"darkcyan"						=> "#008b8b",
		"darkgoldenrod"					=> "#b8860b",
		"darkgray"						=> "#a9a9a9",
		"darkgreen"						=> "#006400",
		"darkkhaki"						=> "#bdb76b",
		"darkmagenta"					=> "#8b008b",
		"darkolivegreen"				=> "#556b2f",
		"darkorange"					=> "#ff8c00",
		"darkorchid"					=> "#9932cc",
		"darkred"						=> "#8b0000",
		"darksalmon"					=> "#e9967a",
		"darkseagreen"					=> "#8fbc8f",
		"darkslateblue"					=> "#483d8b",
		"darkslategray"					=> "#2f4f4f",
		"darkturquoise"					=> "#00ced1",
		"darkviolet"					=> "#9400d3",
		"deeppink"						=> "#ff1493",
		"deepskyblue"					=> "#00bfff",
		"dimgray"						=> "#696969",
		"dodgerblue"					=> "#1e90ff",
		"firebrick"						=> "#b22222",
		"floralwhite"					=> "#fffaf0",
		"forestgreen"					=> "#228b22",
		"fuchsia"						=> "#f0f",
		"gainsboro"						=> "#dcdcdc",
		"ghostwhite"					=> "#f8f8ff",
		"gold"							=> "#ffd700",
		"goldenrod"						=> "#daa520",
		"gray"							=> "#808080",
		"green"							=> "#008000",
		"greenyellow"					=> "#adff2f",
		"honeydew"						=> "#f0fff0",
		"hotpink"						=> "#ff69b4",
		"indianred"						=> "#cd5c5c",
		"indigo"						=> "#4b0082",
		"ivory"							=> "#fffff0",
		"khaki"							=> "#f0e68c",
		"lavender"						=> "#e6e6fa",
		"lavenderblush"					=> "#fff0f5",
		"lawngreen"						=> "#7cfc00",
		"lemonchiffon"					=> "#fffacd",
		"lightblue"						=> "#add8e6",
		"lightcoral"					=> "#f08080",
		"lightcyan"						=> "#e0ffff",
		"lightgoldenrodyellow"			=> "#fafad2",
		"lightgreen"					=> "#90ee90",
		"lightgrey"						=> "#d3d3d3",
		"lightpink"						=> "#ffb6c1",
		"lightsalmon"					=> "#ffa07a",
		"lightseagreen"					=> "#20b2aa",
		"lightskyblue"					=> "#87cefa",
		"lightslategray"				=> "#789",
		"lightsteelblue"				=> "#b0c4de",
		"lightyellow"					=> "#ffffe0",
		"lime"							=> "#0f0",
		"limegreen"						=> "#32cd32",
		"linen"							=> "#faf0e6",
		"maroon"						=> "#800000",
		"mediumaquamarine"				=> "#66cdaa",
		"mediumblue"					=> "#0000cd",
		"mediumorchid"					=> "#ba55d3",
		"mediumpurple"					=> "#9370db",
		"mediumseagreen"				=> "#3cb371",
		"mediumslateblue"				=> "#7b68ee",
		"mediumspringgreen"				=> "#00fa9a",
		"mediumturquoise"				=> "#48d1cc",
		"mediumvioletred"				=> "#c71585",
		"midnightblue"					=> "#191970",
		"mintcream"						=> "#f5fffa",
		"mistyrose"						=> "#ffe4e1",
		"moccasin"						=> "#ffe4b5",
		"navajowhite"					=> "#ffdead",
		"navy"							=> "#000080",
		"oldlace"						=> "#fdf5e6",
		"olive"							=> "#808000",
		"olivedrab"						=> "#6b8e23",
		"orange"						=> "#ffa500",
		"orangered"						=> "#ff4500",
		"orchid"						=> "#da70d6",
		"palegoldenrod"					=> "#eee8aa",
		"palegreen"						=> "#98fb98",
		"paleturquoise"					=> "#afeeee",
		"palevioletred"					=> "#db7093",
		"papayawhip"					=> "#ffefd5",
		"peachpuff"						=> "#ffdab9",
		"peru"							=> "#cd853f",
		"pink"							=> "#ffc0cb",
		"plum"							=> "#dda0dd",
		"powderblue"					=> "#b0e0e6",
		"purple"						=> "#800080",
		"red"							=> "#f00",
		"rosybrown"						=> "#bc8f8f",
		"royalblue"						=> "#4169e1",
		"saddlebrown"					=> "#8b4513",
		"salmon"						=> "#fa8072",
		"sandybrown"					=> "#f4a460",
		"seagreen"						=> "#2e8b57",
		"seashell"						=> "#fff5ee",
		"sienna"						=> "#a0522d",
		"silver"						=> "#c0c0c0",
		"skyblue"						=> "#87ceeb",
		"slateblue"						=> "#6a5acd",
		"slategray"						=> "#708090",
		"snow"							=> "#fffafa",
		"springgreen"					=> "#00ff7f",
		"steelblue"						=> "#4682b4",
		"tan"							=> "#d2b48c",
		"teal"							=> "#008080",
		"thistle"						=> "#d8bfd8",
		"tomato"						=> "#ff6347",
		"turquoise"						=> "#40e0d0",
		"violet"						=> "#ee82ee",
		"wheat"							=> "#f5deb3",
		"white"							=> "#fff",
		"whitesmoke"					=> "#f5f5f5",
		"yellow"						=> "#ff0",
		"yellowgreen"					=> "#9acd32"
		);
	/**
	 * 
	 * @var array
	 */
	private static $fontWeightTransformations = array
 		(
 		"normal"						=> "400",
 		"bold"							=> "700"
 		);
	/**
	 * Css property transformations table. Used to convert CSS3 and proprietary properties to the browser-specific counterparts.
	 * 
	 * @var array
	 */
	private static $propertyTransformations = array
		(
		// Property						Array(Mozilla, Webkit, Opera, Internet Explorer); NULL values are placeholders and will get ignored
		"animation"						=> array(null, "-webkit-animation", null, null),
		"animation-delay"				=> array(null, "-webkit-animation-delay", null, null),
		"animation-direction" 			=> array(null, "-webkit-animation-direction", null, null),
		"animation-duration"			=> array(null, "-webkit-animation-duration", null, null),
		"animation-fill-mode"			=> array(null, "-webkit-animation-fill-mode", null, null),
		"animation-iteration-count"		=> array(null, "-webkit-animation-iteration-count", null, null),
		"animation-name"				=> array(null, "-webkit-animation-name", null, null),
		"animation-play-state"			=> array(null, "-webkit-animation-play-state", null, null),
		"animation-timing-function"		=> array(null, "-webkit-animation-timing-function", null, null),
		"appearance"					=> array("-moz-appearance", "-webkit-appearance", null, null),
		"backface-visibility"			=> array(null, "-webkit-backface-visibility", null, null),
		"background-clip"				=> array(null, "-webkit-background-clip", null, null),
		"background-composite"			=> array(null, "-webkit-background-composite", null, null),
		"background-inline-policy"		=> array("-moz-background-inline-policy", null, null, null),
		"background-origin"				=> array(null, "-webkit-background-origin", null, null),
		"background-position-x"			=> array(null, null, null, "-ms-background-position-x"),
		"background-position-y"			=> array(null, null, null, "-ms-background-position-y"),
		"background-size"				=> array(null, "-webkit-background-size", null, null),
		"behavior"						=> array(null, null, null, "-ms-behavior"),
		"binding"						=> array("-moz-binding", null, null, null),
		"border-after"					=> array(null, "-webkit-border-after", null, null),
		"border-after-color"			=> array(null, "-webkit-border-after-color", null, null),
		"border-after-style"			=> array(null, "-webkit-border-after-style", null, null),
		"border-after-width"			=> array(null, "-webkit-border-after-width", null, null),
		"border-before"					=> array(null, "-webkit-border-before", null, null),
		"border-before-color"			=> array(null, "-webkit-border-before-color", null, null),
		"border-before-style"			=> array(null, "-webkit-border-before-style", null, null),
		"border-before-width"			=> array(null, "-webkit-border-before-width", null, null),
		"border-border-bottom-colors"	=> array("-moz-border-bottom-colors", null, null, null),
		"border-bottom-left-radius"		=> array("-moz-border-radius-bottomleft", "-webkit-border-bottom-left-radius", null, null),
		"border-bottom-right-radius"	=> array("-moz-border-radius-bottomright", "-webkit-border-bottom-right-radius", null, null),
		"border-end"					=> array("-moz-border-end", "-webkit-border-end", null, null),
		"border-end-color"				=> array("-moz-border-end-color", "-webkit-border-end-color", null, null),
		"border-end-style"				=> array("-moz-border-end-style", "-webkit-border-end-style", null, null),
		"border-end-width"				=> array("-moz-border-end-width", "-webkit-border-end-width", null, null),
		"border-fit"					=> array(null, "-webkit-border-fit", null, null),
		"border-horizontal-spacing"		=> array(null, "-webkit-border-horizontal-spacing", null, null),
		"border-image"					=> array("-moz-border-image", "-webkit-border-image", null, null),
		"border-left-colors"			=> array("-moz-border-left-colors", null, null, null),
		"border-radius"					=> array("-moz-border-radius", "-webkit-border-radius", null, null),
		"border-border-right-colors"	=> array("-moz-border-right-colors", null, null, null),
		"border-start"					=> array("-moz-border-start", "-webkit-border-start", null, null),
		"border-start-color"			=> array("-moz-border-start-color", "-webkit-border-start-color", null, null),
		"border-start-style"			=> array("-moz-border-start-style", "-webkit-border-start-style", null, null),
		"border-start-width"			=> array("-moz-border-start-width", "-webkit-border-start-width", null, null),
		"border-top-colors"				=> array("-moz-border-top-colors", null, null, null),
		"border-top-left-radius"		=> array("-moz-border-radius-topleft", "-webkit-border-top-left-radius", null, null),
		"border-top-right-radius"		=> array("-moz-border-radius-topright", "-webkit-border-top-right-radius", null, null),
		"border-vertical-spacing"		=> array(null, "-webkit-border-vertical-spacing", null, null),
		"box-align"						=> array("-moz-box-align", "-webkit-box-align", null, null),
		"box-direction"					=> array("-moz-box-direction", "-webkit-box-direction", null, null),
		"box-flex"						=> array("-moz-box-flex", "-webkit-box-flex", null, null),
		"box-flex-group"				=> array(null, "-webkit-box-flex-group", null, null),
		"box-flex-lines"				=> array(null, "-webkit-box-flex-lines", null, null),
		"box-ordinal-group"				=> array("-moz-box-ordinal-group", "-webkit-box-ordinal-group", null, null),
		"box-orient"					=> array("-moz-box-orient", "-webkit-box-orient", null, null),
		"box-pack"						=> array("-moz-box-pack", "-webkit-box-pack", null, null),
		"box-reflect"					=> array(null, "-webkit-box-reflect", null, null),
		"box-shadow"					=> array("-moz-box-shadow", "-webkit-box-shadow", null, null),
		"box-sizing"					=> array("-moz-box-sizing", null, null, null),
		"color-correction"				=> array(null, "-webkit-color-correction", null, null),
		"column-break-after"			=> array(null, "-webkit-column-break-after", null, null),
		"column-break-before"			=> array(null, "-webkit-column-break-before", null, null),
		"column-break-inside"			=> array(null, "-webkit-column-break-inside", null, null),
		"column-count"					=> array("-moz-column-count", "-webkit-column-count", null, null),
		"column-gap"					=> array("-moz-column-gap", "-webkit-column-gap", null, null),
		"column-rule"					=> array("-moz-column-rule", "-webkit-column-rule", null, null),
		"column-rule-color"				=> array("-moz-column-rule-color", "-webkit-column-rule-color", null, null),
		"column-rule-style"				=> array("-moz-column-rule-style", "-webkit-column-rule-style", null, null),
		"column-rule-width"				=> array("-moz-column-rule-width", "-webkit-column-rule-width", null, null),
		"column-span"					=> array(null, "-webkit-column-span", null, null),
		"column-width"					=> array("-moz-column-width", "-webkit-column-width", null, null),
		"columns"						=> array(null, "-webkit-columns", null, null),
		"filter"						=> array(null, null, null, "-ms-filter"),
		"float-edge"					=> array("-moz-float-edge", null, null, null),
		"font-feature-settings"			=> array("-moz-font-feature-settings", null, null, null),
		"font-language-override"		=> array("-moz-font-language-override", null, null, null),
		"font-size-delta"				=> array(null, "-webkit-font-size-delta", null, null),
		"font-smoothing"				=> array(null, "-webkit-font-smoothing", null, null),
		"force-broken-image-icon"		=> array("-moz-force-broken-image-icon", null, null, null),
		"highlight"						=> array(null, "-webkit-highlight", null, null),
		"hyphenate-character"			=> array(null, "-webkit-hyphenate-character", null, null),
		"hyphenate-locale"				=> array(null, "-webkit-hyphenate-locale", null, null),
		"hyphens"						=> array(null, "-webkit-hyphens", null, null),
		"force-broken-image-icon"		=> array("-moz-image-region", null, null, null),
		"ime-mode"						=> array(null, null, null, "-ms-ime-mode"),
		"interpolation-mode"			=> array(null, null, null, "-ms-interpolation-mode"),
		"layout-flow"					=> array(null, null, null, "-ms-layout-flow"),
		"layout-grid"					=> array(null, null, null, "-ms-layout-grid"),
		"layout-grid-char"				=> array(null, null, null, "-ms-layout-grid-char"),
		"layout-grid-line"				=> array(null, null, null, "-ms-layout-grid-line"),
		"layout-grid-mode"				=> array(null, null, null, "-ms-layout-grid-mode"),
		"layout-grid-type"				=> array(null, null, null, "-ms-layout-grid-type"),
		"line-break"					=> array(null, "-webkit-line-break", null, "-ms-line-break"),
		"line-clamp"					=> array(null, "-webkit-line-clamp", null, null),
		"line-grid-mode"				=> array(null, null, null, "-ms-line-grid-mode"),
		"logical-height"				=> array(null, "-webkit-logical-height", null, null),
		"logical-width"					=> array(null, "-webkit-logical-width", null, null),
		"margin-after"					=> array(null, "-webkit-margin-after", null, null),
		"margin-after-collapse"			=> array(null, "-webkit-margin-after-collapse", null, null),
		"margin-before"					=> array(null, "-webkit-margin-before", null, null),
		"margin-before-collapse"		=> array(null, "-webkit-margin-before-collapse", null, null),
		"margin-bottom-collapse"		=> array(null, "-webkit-margin-bottom-collapse", null, null),
		"margin-collapse"				=> array(null, "-webkit-margin-collapse", null, null),
		"margin-end"					=> array("-moz-margin-end", "-webkit-margin-end", null, null),
		"margin-start"					=> array("-moz-margin-start", "-webkit-margin-start", null, null),
		"margin-top-collapse"			=> array(null, "-webkit-margin-top-collapse", null, null),
		"marquee "						=> array(null, "-webkit-marquee", null, null),
		"marquee-direction"				=> array(null, "-webkit-marquee-direction", null, null),
		"marquee-increment"				=> array(null, "-webkit-marquee-increment", null, null),
		"marquee-repetition"			=> array(null, "-webkit-marquee-repetition", null, null),
		"marquee-speed"					=> array(null, "-webkit-marquee-speed", null, null),
		"marquee-style"					=> array(null, "-webkit-marquee-style", null, null),
		"mask"							=> array(null, "-webkit-mask", null, null),
		"mask-attachment"				=> array(null, "-webkit-mask-attachment", null, null),
		"mask-box-image"				=> array(null, "-webkit-mask-box-image", null, null),
		"mask-clip"						=> array(null, "-webkit-mask-clip", null, null),
		"mask-composite"				=> array(null, "-webkit-mask-composite", null, null),
		"mask-image"					=> array(null, "-webkit-mask-image", null, null),
		"mask-origin"					=> array(null, "-webkit-mask-origin", null, null),
		"mask-position"					=> array(null, "-webkit-mask-position", null, null),
		"mask-position-x"				=> array(null, "-webkit-mask-position-x", null, null),
		"mask-position-y"				=> array(null, "-webkit-mask-position-y", null, null),
		"mask-repeat"					=> array(null, "-webkit-mask-repeat", null, null),
		"mask-repeat-x"					=> array(null, "-webkit-mask-repeat-x", null, null),
		"mask-repeat-y"					=> array(null, "-webkit-mask-repeat-y", null, null),
		"mask-size"						=> array(null, "-webkit-mask-size", null, null),
		"match-nearest-mail-blockquote-color" => array(null, "-webkit-match-nearest-mail-blockquote-color", null, null),
		"max-logical-height"			=> array(null, "-webkit-max-logical-height", null, null),
		"max-logical-width"				=> array(null, "-webkit-max-logical-width", null, null),
		"min-logical-height"			=> array(null, "-webkit-min-logical-height", null, null),
		"min-logical-width"				=> array(null, "-webkit-min-logical-width", null, null),
		"object-fit"					=> array(null, null, "-o-object-fit", null),
		"object-position"				=> array(null, null, "-o-object-position", null),
		"opacity"						=> array(array(__CLASS__, "_opacityTransformation")),
		"outline-radius"				=> array("-moz-outline-radius", null, null, null),
		"outline-bottom-left-radius"	=> array("-moz-outline-radius-bottomleft", null, null, null),
		"outline-bottom-right-radius"	=> array("-moz-outline-radius-bottomright", null, null, null),
		"outline-top-left-radius"		=> array("-moz-outline-radius-topleft", null, null, null),
		"outline-top-right-radius"		=> array("-moz-outline-radius-topright", null, null, null),
		"padding-after"					=> array(null, "-webkit-padding-after", null, null),
		"padding-before"				=> array(null, "-webkit-padding-before", null, null),
		"padding-end"					=> array("-moz-padding-end", "-webkit-padding-end", null, null),
		"padding-start"					=> array("-moz-padding-start", "-webkit-padding-start", null, null),
		"perspective"					=> array(null, "-webkit-perspective", null, null),
		"perspective-origin"			=> array(null, "-webkit-perspective-origin", null, null),
		"perspective-origin-x"			=> array(null, "-webkit-perspective-origin-x", null, null),
		"perspective-origin-y"			=> array(null, "-webkit-perspective-origin-y", null, null),
		"rtl-ordering"					=> array(null, "-webkit-rtl-ordering", null, null),
		"scrollbar-3dlight-color"		=> array(null, null, null, "-ms-scrollbar-3dlight-color"),
		"scrollbar-arrow-color"			=> array(null, null, null, "-ms-scrollbar-arrow-color"),
		"scrollbar-base-color"			=> array(null, null, null, "-ms-scrollbar-base-color"),
		"scrollbar-darkshadow-color"	=> array(null, null, null, "-ms-scrollbar-darkshadow-color"),
		"scrollbar-face-color"			=> array(null, null, null, "-ms-scrollbar-face-color"),
		"scrollbar-highlight-color"		=> array(null, null, null, "-ms-scrollbar-highlight-color"),
		"scrollbar-shadow-color"		=> array(null, null, null, "-ms-scrollbar-shadow-color"),
		"scrollbar-track-color"			=> array(null, null, null, "-ms-scrollbar-track-color"),
		"stack-sizing"					=> array("-moz-stack-sizing", null, null, null),
		"svg-shadow"					=> array(null, "-webkit-svg-shadow", null, null),
		"tab-size"						=> array("-moz-tab-size", null, "-o-tab-size", null),
		"table-baseline"				=> array(null, null, "-o-table-baseline", null),
		"text-align-last"				=> array(null, null, null, "-ms-text-align-last"),
		"text-autospace"				=> array(null, null, null, "-ms-text-autospace"),
		"text-combine"					=> array(null, "-webkit-text-combine", null, null),
		"text-decorations-in-effect"	=> array(null, "-webkit-text-decorations-in-effect", null, null),
		"text-emphasis"					=> array(null, "-webkit-text-emphasis", null, null),
		"text-emphasis-color"			=> array(null, "-webkit-text-emphasis-color", null, null),
		"text-emphasis-position"		=> array(null, "-webkit-text-emphasis-position", null, null),
		"text-emphasis-style"			=> array(null, "-webkit-text-emphasis-style", null, null),
		"text-fill-color"				=> array(null, "-webkit-text-fill-color", null, null),
		"text-justify"					=> array(null, null, null, "-ms-text-justify"),
		"text-kashida-space"			=> array(null, null, null, "-ms-text-kashida-space"),
		"text-overflow"					=> array(null, null, "-o-text-overflow", "-ms-text-overflow"),
		"text-security"					=> array(null, "-webkit-text-security", null, null),
		"text-size-adjust"				=> array(null, "-webkit-text-size-adjust", null, "-ms-text-size-adjust"),
		"text-stroke"					=> array(null, "-webkit-text-stroke", null, null),
		"text-stroke-color"				=> array(null, "-webkit-text-stroke-color", null, null),
		"text-stroke-width"				=> array(null, "-webkit-text-stroke-width", null, null),
		"text-underline-position"		=> array(null, null, null, "-ms-text-underline-position"),
		"transform"						=> array("-moz-transform", "-webkit-transform", "-o-transform", null),
		"transform-origin"				=> array("-moz-transform-origin", "-webkit-transform-origin", "-o-transform-origin", null),
		"transform-origin-x"			=> array(null, "-webkit-transform-origin-x", null, null),
		"transform-origin-y"			=> array(null, "-webkit-transform-origin-y", null, null),
		"transform-origin-z"			=> array(null, "-webkit-transform-origin-z", null, null),
		"transform-style"				=> array(null, "-webkit-transform-style", null, null),
		"transition"					=> array("-moz-transition", "-webkit-transition", "-o-transition", null),
		"transition-delay"				=> array("-moz-transition-delay", "-webkit-transition-delay", "-o-transition-delay", null),
		"transition-duration"			=> array("-moz-transition-duration", "-webkit-transition-duration", "-o-transition-duration", null),
		"transition-property"			=> array("-moz-transition-property", "-webkit-transition-property", "-o-transition-property", null),
		"transition-timing-function"	=> array("-moz-transition-timing-function", "-webkit-transition-timing-function", "-o-transition-timing-function", null),
		"user-drag"						=> array(null, "-webkit-user-drag", null, null),
		"user-focus"					=> array("-moz-user-focus", null, null, null),
		"user-input"					=> array("-moz-user-input", null, null, null),
		"user-modify"					=> array("-moz-user-modify", "-webkit-user-modify", null, null),
		"user-select"					=> array("-moz-user-select", "-webkit-user-select", null, null),
		"white-space"					=> array(array(__CLASS__, "_whiteSpacePreWrapTransformation")),
		"window-shadow"					=> array("-moz-window-shadow", null, null, null),
		"word-break"					=> array(null, null, null, "-ms-word-break"),
		"word-wrap"						=> array(null, null, null, "-ms-word-wrap"),
		"writing-mode"					=> array(null, "-webkit-writing-mode", null, "-ms-writing-mode"),
		"zoom"							=> array(null, null, null, "-ms-zoom")
		);
	/**
	 * Minifies the Css.
	 * 
	 * @param string $css Css as string
	 * @param array $config {@link http://code.google.com/p/cssmin/wiki/Configuration Configuration} as array [optional]
	 * @return string Minified css
	 */
	public static function minify($css, $config = array())
		{
		$tokens = self::parse($css);
		// Normalize configuration parameters
		if (count($config) > 0)
			{
			$config = array_combine(array_map("trim", array_map("strtolower", array_keys($config))), array_values($config));
			}
		$config = array_merge(self::$defaultConfiguration, $config);
		// Minification options/variables
		$sRemoveEmptyBlocks				= $config["remove-empty-blocks"];
		$sRemoveEmptyRulesets			= $config["remove-empty-rulesets"];
		$sRemoveLastSemicolon			= $config["remove-last-semicolons"];
		$sRemoveComments				= $config["remove-comments"];
		$sConvertCss3Properties 		= $config["convert-css3-properties"];
		$sCss3Variables					= array();
		$sConvertFontWeightValues		= $config["convert-font-weight-values"];
		$rConvertFontWeightValues		= "/(^|\s)+(normal|bold)(\s|$)+/ie";
		$rConvertFontWeightValuesR		= "'\\1'.self::\$fontWeightTransformations['\\2'].'\\3'";
		$sConvertNamedColorValues		= $config["convert-named-color-values"];
		$rConvertNamedColorValues 		= "/(^|\s)+(" . implode("|", array_keys(self::$colorTransformations)) . ")(\s|$)+/ie";
		$rConvertNamedColorValuesR 		= "'\\1'.self::\$colorTransformations['\\2'].'\\3'";
		$sConvertRgbColorValues			= $config["convert-rgb-color-values"];
		$rConvertRgbColorValues			= "/rgb\s*\(\s*([0-9%]+)\s*,\s*([0-9%]+)\s*,\s*([0-9%]+)\s*\)/iS";
		$sConvertHslColorValues			= $config["convert-rgb-color-values"];
		$rConvertHslColorValues			= "/^hsl\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*%\s*,\s*([0-9]+)\s*%\s*\)/iS";
		$sCompressColorValues			= $config["compress-color-values"];
		$rCompressColorValues			= "/\#([0-9a-f]{6})/iS";
		$sCompressUnitValues			= $config["compress-unit-values"];
		$rCompressUnitValues1			= "/(^| |-)0\.([0-9]+?)(0+)?(%|em|ex|px|in|cm|mm|pt|pc)/iS";
		$rCompressUnitValues1R			= "\${1}.\${2}\${4}";
		$rCompressUnitValues2			= "/(^| )-?(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/iS";
		$rCompressUnitValues2R			= "\${1}0";
		$sEmulateCcss3Variables			= $config["emulate-css3-variables"];
		$sImportImports					= $config["import-imports"];
		$sImportBasePath				= $config["import-base-path"];
		$sImportRemoveInvalid			= $config["import-remove-invalid"];
		$sImportStartBlockTokens		= array(self::T_AT_MEDIA_START, self::T_AT_FONT_FACE_START, self::T_AT_PAGE_START);
		$sImportEndBlockTokens 			= array(self::T_AT_MEDIA_END, self::T_AT_FONT_FACE_END, self::T_AT_PAGE_END);
		$sImportStatementTokens 		= array(self::T_AT_RULE, self::T_AT_IMPORT);
		$sImportMediaEndToken			= array(self::T_AT_MEDIA_END);
		$sImportImportedFiles			= array();
		$sRemoveTokens					= array_filter(array(self::T_NULL, ($sRemoveComments ? self::T_COMMENT : false)));
		
		/*
		 * Import @import at-rules.
		 * 
		 * If the @import at-rule defines one or more media type the imported tokens wil get wrapped into T_AT_MEDIA_START 
		 * and T_AT_MEDIA_END token. @font-face, @media and @page at-rule blocks and at-rule statements will get excluded
		 * of the wraping.
		 * 
		 * Additional @media at-rule blocks with media types not defined in the @import at-rule have to get removed.
		 * Browsers ignore @media at-rule block with media types incompatible to the media types defined in the @import
		 * at-rule.
		 * 
		 * Also @import rules require special treatment. If a included @import at-rule has no media type or only the 
		 * "all" media type defined the media type of the @import at-rule will get set to the ones defined in the parent
		 * @import at-rule. Media types not defined in the parent @import at-rule will get filtered. @import at-rule
		 * with not matching media types will get removed.
		 * 
		 * For compression if a @media at-rule block is defined the same media type as the @import at-rule the 
		 * T_AT_MEDIA_START and T_AT_MEDIA_END tokens will get removed.
		 */
		if ($sImportImports && is_dir($sImportBasePath))
			{
			$importFile				= "";
			$importTokens			= array();
			$importMediaStartToken	= array(self::T_NULL);
			$importBlocks			= array();
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				if ($tokens[$i][0] == self::T_AT_IMPORT && file_exists($sImportBasePath . $tokens[$i][1]))
					{
					$importFile = $sImportBasePath . $tokens[$i][1];
					// Import file already imported; remove this @import at-rule to prevent any recursion
					if (in_array($importFile, $sImportImportedFiles))
						{
						$tokens[$i] = array(self::T_NULL);
						}
					else
						{
						$sImportImportedFiles[]	= $sImportBasePath . $tokens[$i][1];
						$importTokens			= self::parse(file_get_contents($importFile));
						// The @import at-rule has media types defined requires special handling
						if (count($tokens[$i][2]) > 0 && !(count($tokens[$i][2]) == 1 && $tokens[$i][2][0] == "all"))
							{
							// Create T_AT_MEDIA_START token used for wrapping and array for blocks
							$importMediaStartToken	= array(self::T_AT_MEDIA_START, $tokens[$i][2]);
							$importBlocks			= array();
							// Filter or set media types of @import at-rule or remove the @import at-rule if no media type is matching the parent @import at-rule
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_IMPORT)
									{
									// @import at-rule defines no media type or only the "all" media type; set the media types to the one defined in the parent @import at-rule
									if (count($importTokens[$ii][2]) == 0 || (count($importTokens[$ii][2]) == 1 && $importTokens[$ii][2][0]) == "all")
										{
										$importTokens[$ii][2] = $tokens[$i][2];
										}
									// @import at-rule defineds one or more media types; filter out media types not matching with the  parent @import at-rule
									elseif (count($importTokens[$ii][2] > 0))
										{
										foreach ($importTokens[$ii][2] as $index => $mediaType)
											{
											if (!in_array($mediaType, $tokens[$i][2]))
												{
												unset($importTokens[$ii][2][$index]);
												}
											}
										$importTokens[$ii][2] = array_values($importTokens[$ii][2]);
										// If there are no media types left in the @import at-rule remove the @import at-rule
										if (count($importTokens[$ii][2]) == 0)
											{
											$importTokens[$ii] = array(self::T_NULL);
											}
										}
									}
								}
							// Remove media types of @media at-rule block not defined in the @import at-rule
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_MEDIA_START)
									{
									foreach ($importTokens[$ii][1] as $index => $mediaType)
										{
										if (!in_array($mediaType, $tokens[$i][2]))
											{
											unset($importTokens[$ii][1][$index]);
											}
										$importTokens[$ii][1] = array_values($importTokens[$ii][1]);
										}
									}
								}
							// If no media types left of the @media at-rule block remove the complete block
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_MEDIA_START)
									{
									if (count($importTokens[$ii][1]) == 0)
										{
										for ($iii = $ii; $iii < $ll; $iii++)
											{
											if ($importTokens[$iii][0] == self::T_AT_MEDIA_END)
												{
												break;
												}
											}
										if ($importTokens[$iii][0] == self::T_AT_MEDIA_END)
											{
											array_splice($importTokens, $ii, $iii - $ii + 1, array());
											$ll = count($importTokens);
											}
										}
									}
								}
							// If the media types of the @media at-rule equals the media types defined in the @import at-rule remove the T_AT_MEDIA_START and T_AT_MEDIA_END token
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_MEDIA_START && count(array_diff($tokens[$i][2], $importTokens[$ii][1])) == 0)
									{
									for ($iii = $ii; $iii < $ll; $iii++)
										{
										if ($importTokens[$iii][0] == self::T_AT_MEDIA_END)
											{
											break;
											}
										}
									if ($importTokens[$iii][0] == self::T_AT_MEDIA_END)
										{
										unset($importTokens[$ii]);
										unset($importTokens[$iii]);
										$importTokens = array_values($importTokens);
										$ll = count($importTokens);
										}
									}
								}
							// Extract @import at-rule tokens
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_IMPORT)
									{
									$importBlocks = array_merge($importBlocks, array_splice($importTokens, $ii, 1, array()));
									$ll = count($importTokens);
									}
								}
							// Extract T_AT_RULE tokens
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if ($importTokens[$ii][0] == self::T_AT_RULE)
									{
									$importBlocks = array_merge($importBlocks, array_splice($importTokens, $ii, 1, array()));
									$ll = count($importTokens);
									}
								}
							// Extract the @font-face, @media and @page at-rule block
							for($ii = 0, $ll = count($importTokens); $ii < $ll; $ii++)
								{
								if (in_array($importTokens[$ii][0], $sImportStartBlockTokens))
									{
									for ($iii = $ii; $iii < $ll; $iii++)
										{
										if (in_array($importTokens[$iii][0], $sImportEndBlockTokens))
											{
											break;
											}
										}
									if (isset($importTokens[$iii][0]) && in_array($importTokens[$iii][0], $sImportEndBlockTokens))
										{
										$importBlocks = array_merge($importBlocks, array_splice($importTokens, $ii, $iii - $ii + 1, array()));
										$ll = count($importTokens);
										}
									}
								}
							// Create the array with imported tokens for @import at-rules with defined media types
							$importTokens = array_merge($importBlocks, array($importMediaStartToken), $importTokens, array($sImportMediaEndToken));
							}
						array_splice($tokens, $i, 1, $importTokens);
						// Modify parameters of the for-loop
						$i--;
						$l = count($tokens);
						}
					}
				}
			}
		/*
		 * Remove tokens.
		 * 
		 * Remove tokens not required for minification. Defaults to T_NULL and T_COMMENT tokens.
		 * 
		 * If CSS Level 3 Variables (emulate-css3-variables) is disabled add T_AT_VARIABLES_START, T_VARIABLE_DECLARATION 
		 * and T_AT_VARIABLES_END tokens.
		 * 
		 * If the configuration options "import-imports" and "import-remove-invalid" is enabled remove also remaining 
		 * T_AT_IMPORT tokens.
		 */
		if (!$sEmulateCcss3Variables)
			{
			$sRemoveTokens = array_merge($sRemoveTokens, array(self::T_AT_VARIABLES_START, self::T_VARIABLE_DECLARATION, self::T_AT_VARIABLES_END));
			}
		if ($sImportImports && is_dir($sImportBasePath) && $sImportRemoveInvalid)
			{
			$sRemoveTokens[] = self::T_AT_IMPORT;
			}
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (in_array($tokens[$i][0], $sRemoveTokens))
				{
				unset($tokens[$i]);
				}
			}
		$tokens = array_values($tokens);
		/*
		 * Remove empty rulesets
		 * 
		 * The ruleset is empty if it only contains the tokens T_RULESET_START, T_SELECTORS, T_DECLARATIONS_START, 
		 * T_DECLARATIONS_END and T_RULESET_END
		 */
		if ($sRemoveEmptyRulesets)
			{
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				if ($tokens[$i][0] == self::T_RULESET_START && $tokens[$i+4][0] == self::T_RULESET_END)
					{
					unset($tokens[$i]); 	// T_RULESET_START
					unset($tokens[++$i]);	// T_SELECTORS
					unset($tokens[++$i]);	// T_DECLARATIONS_START
					unset($tokens[++$i]);	// T_DECLARATIONS_END
					unset($tokens[++$i]);	// T_RULESET_END
					}
				}
			$tokens = array_values($tokens);
			}
		/*
		 * Remove empty @media, @font-face or @page blocks
		 */
		if ($sRemoveEmptyBlocks)
			{
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				if (($tokens[$i][0] == self::T_AT_MEDIA_START && $tokens[$i+1][0] == self::T_AT_MEDIA_END)
					|| ($tokens[$i][0] == self::T_AT_FONT_FACE_START && $tokens[$i+1][0] == self::T_AT_FONT_FACE_END)
					|| ($tokens[$i][0] == self::T_AT_PAGE_START && $tokens[$i+1][0] == self::T_AT_PAGE_END))
					{
					unset($tokens[$i]);		// T_AT_MEDIA_START, T_AT_FONT_FACE_START, T_AT_PAGE_START
					unset($tokens[++$i]);	// T_AT_MEDIA_END, T_AT_FONT_FACE_END, T_AT_PAGE_END
					}
				}
			$tokens = array_values($tokens);
			}
		/*
		 * Parse CSS Level 3 variables if the configuration option "emulate-css3-variables" is enabled. 
		 */
		if ($sEmulateCcss3Variables)
			{
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				// Found a variable declaration
				if ($tokens[$i][0] == self::T_VARIABLE_DECLARATION)
					{
					for($i2 = 0, $l2 = count($tokens[$i][3]); $i2 < $l2; $i2++)
						{
						// Create the scope (all, screen, print, etc.) if not defined
						if (!isset($sCss3Variables[$tokens[$i][3][$i2]]))
							{
							$sCss3Variables[$tokens[$i][3][$i2]] = array();
							}
						// Store variable and value
						$sCss3Variables[$tokens[$i][3][$i2]][$tokens[$i][1]] = $tokens[$i][2];
						}
					}
				}
			}
		/*
		 * Conversion and compression
		 */
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if ($tokens[$i][0] == self::T_DECLARATION)
				{
				/*
				 * Search for CSS Level 3 variable statement if the configuration option "emulate-css3-variables" is
				 * enabled. 
				 * 
				 * The variable value depends on the media type of the declaration. Primary the variable value
				 * of the media type of the declaration will get used if available. If no variable value for the media 
				 * type of the declaration is defined use the variable value definition for the media type "all". 
				 */
				if ($sEmulateCcss3Variables)
					{
					// Found a 'var' statement
					if (strtolower(substr($tokens[$i][2], 0, 4)) == "var(" && substr($tokens[$i][2], -1, 1) == ")")
						{
						// Extract the variable name
						$variable = trim(substr($tokens[$i][2], 4, -1));
						// Append the media type "all" to the declaration
						$tokens[$i][3][] = "all"; 
						for($i2 = 0, $l2 = count($tokens[$i][3]); $i2 < $l2; $i2++)
							{
							// Found a variable value for the current media type scope
							if (isset($sCss3Variables[$tokens[$i][3][$i2]][$variable]))
								{
								$tokens[$i][2] = $sCss3Variables[$tokens[$i][3][$i2]][$variable];
								break;
								}
							}
						}
					}
				/* 
				 * Convert font-weight values if the configuration option "convert-font-weight-values" is enabled.
				 *  
				 * Will convert font weight values "normal" to "400" and "bold" to "700". Restricted to "font-weight"
				 * and "font" declarations.
				 */
				if ($sConvertFontWeightValues && in_array(strtolower($tokens[$i][1]), array("font-weight", "font")) && preg_match($rConvertFontWeightValues, $tokens[$i][2]))
					{
					$tokens[$i][2] = preg_replace($rConvertFontWeightValues, $rConvertFontWeightValuesR, $tokens[$i][2]);
					}
				/**
				 * Compress unit values if the configuration option "compress-unit-values" is enabled.
				 * 
				 * This will compress:
				 * 	1. "0.5px" to ".5px"
				 * 	2. "0px" to "0"
				 * 	3. "0 0 0 0", "0 0 0" or "0 0" to "0"
				 */
				if ($sCompressUnitValues)
					{
					$tokens[$i][2] = preg_replace($rCompressUnitValues1, $rCompressUnitValues1R, $tokens[$i][2]);
					$tokens[$i][2] = preg_replace($rCompressUnitValues2, $rCompressUnitValues2R, $tokens[$i][2]);
					if (strtolower($tokens[$i][1]) != "background-position")
						{
						if ($tokens[$i][2] == "0 0 0 0" || $tokens[$i][2] == "0 0 0" || $tokens[$i][2] == "0 0") {$tokens[$i][2] = "0";}
						}
					}
				/*
				 * Convert RGB color values if the configuration option "convert-rgb-color-values" is enabled.
				 * 
				 * This will convert values like "rgb(200,60%,5)" to "#c89905".
				 */
				if ($sConvertRgbColorValues && preg_match($rConvertRgbColorValues, $tokens[$i][2], $m))
					{
					for ($i2 = 1, $l2 = count($m); $i2 < $l2; $i2++)
						{
						if (strpos("%", $m[$i2]) !== false)
							{
							$m[$i2] = substr($m[$i2], 0, -1);
							$m[$i2] = (int) (256 * ($m[$i2] / 100));
							}
						$m[$i2] = str_pad(dechex($m[$i2]),  2, "0", STR_PAD_LEFT);
						}
					$tokens[$i][2] = str_replace($m[0], "#" . $m[1] . $m[2] . $m[3], $tokens[$i][2]);
					}
				/**
				 * Convert HSL color values if the configuration option "convert-hsl-color-values" is enabled.
				 * 
				 * This will convert values like "hsl(232,36%,48%)" to "#4e5aa7".
				 */
				if ($sConvertHslColorValues && preg_match($rConvertHslColorValues, $tokens[$i][2], $m))
					{
					$tokens[$i][2] = str_replace($m[0], self::_hsl2hex($m[1], $m[2], $m[3]), $tokens[$i][2]);
					}
				/**
				 * Compress color values if the configuration option "compress-color-values" is enabled.
				 * 
				 * This will convert color value like "#aabbcc" to their short notation "#abc". 
				 */
				if ($sCompressColorValues && preg_match($rCompressColorValues, $tokens[$i][2], $m))
					{
					$m[1] = strtolower($m[1]);
					if (substr($m[1], 0, 1) == substr($m[1], 1, 1) && substr($m[1], 2, 1) == substr($m[1], 3, 1) && substr($m[1], 4, 1) == substr($m[1], 5, 1))
						{
						$tokens[$i][2] = str_replace($m[0], "#" . substr($m[1], 0, 1) . substr($m[1], 2, 1) . substr($m[1], 4, 1), $tokens[$i][2]);
						}
					}
				/**
				 * Convert named color values if the configuration option "convert-named-color-values" is enabled.
				 * 
				 * This will convert named color value like "black" to their hexadecimal notation "#000".
				 */
				if ($sConvertNamedColorValues && preg_match($rConvertNamedColorValues, $tokens[$i][2]))
					{
					$tokens[$i][2] = preg_replace($rConvertNamedColorValues, $rConvertNamedColorValuesR, $tokens[$i][2]);
					}
				}
			}
		/*
		 * Create minified css
		 */
		$r = "";
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			// T_AT_RULE
			if ($tokens[$i][0] == self::T_AT_RULE)
				{
				$r .= "@" . $tokens[$i][1] . " " . $tokens[$i][2] . ";";
				}
			// T_AT_IMPORT
			if ($tokens[$i][0] == self::T_AT_IMPORT)
				{
				$r .= "@import \"" . $tokens[$i][1] . "\" " . implode(",", $tokens[$i][2]) . ";";
				}
			// T_AT_MEDIA_START
			elseif ($tokens[$i][0] == self::T_AT_MEDIA_START)
				{
				if (count($tokens[$i][1]) == 1 && $tokens[$i][1][0] == "all")
					{
					$r .= "@media{";
					}
				else
					{
					$r .= "@media " . implode(",", $tokens[$i][1]) . "{";
					}
				}
			// T_AT_FONT_FACE_START
			elseif ($tokens[$i][0] == self::T_AT_FONT_FACE_START)
				{
				$r .= "@font-face{";
				}
			// T_FONT_FACE_DECLARATION
			elseif ($tokens[$i][0] == self::T_FONT_FACE_DECLARATION)
				{
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && $tokens[$i+1][0] == self::T_AT_FONT_FACE_END ? "" : ";");
				}
			// T_AT_PAGE_START
			elseif ($tokens[$i][0] == self::T_AT_PAGE_START)
				{
				$r .= "@page{";
				}
			// T_PAGE_DECLARATION
			elseif ($tokens[$i][0] == self::T_PAGE_DECLARATION)
				{
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && $tokens[$i+1][0] == self::T_AT_PAGE_END ? "" : ";");
				}
			// T_SELECTORS
			elseif ($tokens[$i][0] == self::T_SELECTORS)
				{
				$r .= implode(",", $tokens[$i][1]);
				}
			// Start of declarations
			elseif ($tokens[$i][0] == self::T_DECLARATIONS_START)
				{
				$r .= "{";
				}
			// T_DECLARATION
			elseif ($tokens[$i][0] == self::T_DECLARATION)
				{
				// Convert CSS Level 3 and browser specific properties if "convert-css3-properties" is enabled
				if ($sConvertCss3Properties && isset(self::$propertyTransformations[$tokens[$i][1]]))
					{
					foreach (self::$propertyTransformations[$tokens[$i][1]] as $value)
						{
						if (!is_null($value) && !is_array($value))
							{
							$r .= $value . ":" . $tokens[$i][2] . ";";
							}
						elseif (is_array($value) && is_callable($value))
							{
							$r .= call_user_func_array($value, array($tokens[$i][1], $tokens[$i][2]));
							}
						}
					}
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && (isset($tokens[$i+1]) &&$tokens[$i+1][0] == self::T_DECLARATIONS_END) ? "" : ";");
				}
			// T_DECLARATIONS_END, T_AT_MEDIA_END, T_AT_FONT_FACE_END, T_AT_PAGE_END
			elseif (in_array($tokens[$i][0], array(self::T_DECLARATIONS_END, self::T_AT_MEDIA_END, self::T_AT_FONT_FACE_END, self::T_AT_PAGE_END)))
				{
				$r .= "}";
				}
			elseif ($tokens[$i][0] == self::T_COMMENT)
				{
				$r .= "\n" . $tokens[$i][1] . "\n";
				}
			else
				{
				// Tokens with no output: T_NULL, T_COMMENT, T_RULESET_START, T_RULESET_END, T_AT_VARIABLES_START, T_VARIABLE_DECLARATION and T_AT_VARIABLES_END
				}
			}
		return $r;
		}
	/**
	 * Parses the Css and returns an array of tokens.
	 * 
	 * @param string $css
	 * @return array Array of tokens
	 */
	public static function parse($css)
		{
		/*
		 * Settings
		 */
		$sDefaultScope		= array("all");						// Default scope
		$sDefaultTrim		= " \t\n\r\0\x0B";					// Default trim charlist
		$sTokenChars		= "@{}();:\n\"'/*,";				// Tokens triggering parser processing
		$sWhitespaceChars	= $sDefaultTrim;					// Whitespace chars
		/*
		 * Basic variables
		 */
		$c					= null;								// Current char
		$p					= null;								// Previous char
		$buffer 			= "";								// Buffer
		$errors				= array();
		$saveBuffer			= "";								// Saved buffer
		$state				= array(self::T_DOCUMENT);			// State stack
		$currentState		= self::T_DOCUMENT;					// Current state
		$scope				= $sDefaultScope;					// Current scope
		$stringChar			= null;								// Current string delimiter char
		$isFilterWs			= true;								// Filter double whitespaces? Will get disabled for comments, selectors, etc.
		$selectors			= array();							// Array with collected selectors
		$importUrl			= "";								// @import Url
		$line				= 1;								// Line
		$r 					= array();							// Return value
		/*
		 * Prepare: normalize line endings
		 */
		$css = str_replace("\r\n", "\n", $css); // Windows to Unix line endings
		$css = str_replace("\r", "\n", $css); // Mac to Unix line endings
		/**
		 * Parse:
		 */
		for ($i = 0, $l = strlen($css); $i < $l; $i++)
			{
			// Set the current Char
			$c = substr($css, $i, 1);
			// Increments line number on line endings
			if ($c == "\n")
				{
				$line++;
				}
			// Whitespace handling
			if ($isFilterWs && strpos($sWhitespaceChars, $c) !== false)
				{
				// Filter double whitespaces if the previous char is also a whitespace
				if (strpos($sWhitespaceChars, $p) !== false)
					{
					continue;
					}
				// Normalize whitespace chars to space
				elseif ($c != " " && strpos($sWhitespaceChars, $c) !== false)
					{
					$c = " ";
					}
				}
			$buffer .= $c;
			// Extended processing only if the current char is a token char
			if (strpos($sTokenChars, $c) !== false)
				{
				// Set the current state
				$currentState = $state[count($state) - 1];
				/*
				 * Start of comment
				 * 
				 * The current buffer will get saved and restored at the end of the comment because comments are 
				 * allowed within many parsable elements.
				 */
				if ($p == "/" && $c == "*" && $currentState != self::T_STRING && $currentState != self::T_COMMENT)
					{
					$saveBuffer = substr($buffer, 0, -2); // Save the current buffer 
					$buffer 	= $p . $c;
					$isFilterWs	= false;
					array_push($state, self::T_COMMENT);
					}
				/*
				 * End of comment
				 */
				elseif ($p == "*" && $c == "/" && $currentState == self::T_COMMENT)
					{
					$r[]		= array(self::T_COMMENT, trim($buffer));
					$buffer		= $saveBuffer; // Restore the buffer
					$isFilterWs	= true;
					array_pop($state);
					}
				/*
				 * Start of string
				 */
				elseif (($c == "\"" || $c == "'") && $currentState != self::T_STRING && $currentState != self::T_COMMENT && $currentState != self::T_STRING_URL)
					{
					$stringChar	= $c;
					$isFilterWs	= false;
					array_push($state, self::T_STRING);
					}
				/**
				 * Escaped LF in string => remove escape backslash and LF
				 */
				elseif ($c == "\n" && $p == "\\" && $currentState == self::T_STRING)
					{
					$buffer = substr($buffer, 0, -2);
					}
				/**
				 * Parse error: Unescaped LF in string literal
				 */
				elseif ($c == "\n" && $p != "\\" && $currentState == self::T_STRING)
					{
					$errorStart	= strrpos($css, "\n", - ($l - strrpos($css, ":", -($l - $i + 2))));
					$errorEnd	= strpos($css, "\n", $errorStart + 1);
					$errorLine = trim(substr($css, $errorStart, $errorEnd - $errorStart));
					trigger_error("Line #" . $line . ": Unescaped line ending in string literal:\n<code>" . $errorLine . "_</code>", E_USER_WARNING);
					$buffer		= substr($buffer, 0, -1) . $stringChar; // Replace the LF with the current string char
					$stringChar	= null;
					$isFilterWs	= true;
					array_pop($state);
					}
				/*
				 * End of string
				 */
				elseif ($c === $stringChar && $currentState == self::T_STRING)
					{
					if ($p == "\\") // Previous char is a escape char
						{
						$count = 1; 
						$i2 = $i -2;
						while (substr($css, $i2, 1) == "\\")
							{
							$count++;
							$i2--;
							}
						// if count of escape chars is uneven => continue with string...
						if ($count % 2)
							{
							continue;
							}
						}
					// ...else end the string
					$isFilterWs	= true;
					// Special handling for @import url strings
					if ($state[count($state) - 2] == self::T_AT_IMPORT)
						{
						$importUrl	= $buffer;
						$buffer		= "";
						}
					array_pop($state);
					$stringChar = null;
					}
				/**
				 * Start of expression string property
				 */
				elseif ($c == "(" && ($currentState != self::T_COMMENT && $currentState != self::T_STRING && $currentState != self::T_STRING_URL) && strtolower(substr($css, $i - 10, 10) == "expression") 
					&& ($currentState == self::T_DECLARATION || $currentState == self::T_FONT_FACE_DECLARATION || $currentState == self::T_PAGE_DECLARATION || $currentState == self::T_VARIABLE_DECLARATION))
					{
					array_push($state, self::T_STRING_EXPRESSION);
					}
				/**
				 * End of expression string property
				 */
				elseif (($c == ";" || $c == "}") && $p == ")" && $currentState == self::T_STRING_EXPRESSION)
					{
					$buffer = substr($buffer, 0, -2);
					array_pop($state);
					$i = $i - 2;
					}
				/**
				 * Start of url string property
				 */
				elseif ($c == "(" && ($currentState != self::T_COMMENT && $currentState != self::T_STRING && $currentState != self::T_STRING_EXPRESSION) && strtolower(substr($css, $i - 3, 3) == "url") 
					&& ($currentState == self::T_DECLARATION || $currentState == self::T_FONT_FACE_DECLARATION || $currentState == self::T_PAGE_DECLARATION || $currentState == self::T_VARIABLE_DECLARATION || $currentState == self::T_AT_IMPORT))
					{
					array_push($state, self::T_STRING_URL);
					}
				/**
				 * End of url string property
				 */
				elseif (($c == ")" || $c == "\n") && $currentState == self::T_STRING_URL)
					{
					if ($p == "\\")
						{
						continue;
						}
					// Special handling for @import urls
					if ($state[count($state) - 2] == self::T_AT_IMPORT)
						{
						$importUrl = $buffer;
						$buffer = "";
						}
					array_pop($state);
					}
				/*
				 * Start of at-rule @media block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 6)) == "@media")
					{
					$i			= $i + 6;
					$buffer 	= "";
					array_push($state, self::T_AT_MEDIA_START);
					}
				/*
				 * At-rule @media block media types
				 */
				elseif ($c == "{" && $currentState == self::T_AT_MEDIA_START)
					{
					$buffer 	= strtolower(trim($buffer, $sDefaultTrim . "{"));
					$scope		= $buffer != "" ? array_filter(array_map("trim", explode(",", $buffer))) : $sDefaultScope;
					$r[]		= array(self::T_AT_MEDIA_START, $scope);
					$i			= $i++;
					$buffer		= "";
					array_pop($state);
					array_push($state, self::T_AT_MEDIA);
					}
				/*
				 * End of at-rule @media block
				 */
				elseif ($currentState == self::T_AT_MEDIA && $c == "}")
					{
					$r[]		= array(self::T_AT_MEDIA_END);
					$scope		= $sDefaultScope;
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @font-face block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 10)) == "@font-face")
					{
					$r[]		= array(self::T_AT_FONT_FACE_START);
					$i			= $i + 10;
					$buffer 	= "";
					array_push($state, self::T_AT_FONT_FACE);
					}
				/*
				 * @font-face declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_FONT_FACE)
					{
					$property	= trim($buffer, $sDefaultTrim . ":{");
					$buffer		= "";
					array_push($state, self::T_FONT_FACE_DECLARATION);
					}
				/*
				 * @font-face declaration: Value
				 */
				elseif (($c == ";" || $c == "}") && $currentState == self::T_FONT_FACE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_FONT_FACE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @font-face declaration closed with a right curly brace => closes @font-face block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_FONT_FACE_END);
						}
					}
				/*
				 * Parse error: @font-face declaration value 
				 */
				elseif ($c == ":" && $currentState == self::T_FONT_FACE_DECLARATION)
					{
					$errorStart	= strrpos($css, "\n", -($l - strrpos($css, ":", -($l - $i + 2))));
					$errorEnd	= strpos($css, "\n", $errorStart + 1);
					$errorLine = trim(substr($css, $errorStart, $errorEnd - $errorStart));
					trigger_error("Line #" . $line . ": Unterminated @font-face declaration:\n<code>" . $errorLine . "_</code>", E_USER_WARNING);
					}
				/*
				 * End of at-rule @font-face block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_FONT_FACE)
					{
					$r[]		= array(self::T_AT_FONT_FACE_END);
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @page block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 5)) == "@page")
					{
					$r[]		= array(self::T_AT_PAGE_START);
					$i			= $i + 5;
					$buffer 	= "";
					array_push($state, self::T_AT_PAGE);
					}
				/*
				 * @page declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_PAGE)
					{
					$property	= trim($buffer, $sDefaultTrim . ":{");
					$buffer		= "";
					array_push($state, self::T_PAGE_DECLARATION);
					}
				/*
				 * @page declaration: Value
				 */
				elseif (($c == ";" || $c == "}") && $currentState == self::T_PAGE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_PAGE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @page declaration closed with a right curly brace => closes @page block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_PAGE_END);
						}
					}
				/*
				 * 
				 */
				elseif ($c == ":" && $currentState == self::T_PAGE_DECLARATION)
					{
					$errorStart	= strrpos($css, "\n", -($l - strrpos($css, ":", -($l - $i + 2))));
					$errorEnd	= strpos($css, "\n", $errorStart + 1);
					$errorLine	= trim(substr($css, $errorStart, $errorEnd - $errorStart));
					trigger_error("Line #" . $line . ": Unterminated @page declaration:\n<code>" . $errorLine . "_</code>", E_USER_WARNING);
					}
				/*
				 * End of at-rule @page block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_PAGE)
					{
					$r[]		= array(self::T_AT_PAGE_END);
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @variables block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT &&  strtolower(substr($css, $i, 10)) == "@variables")
					{
					$i			= $i + 10;
					$buffer 	= "";
					array_push($state, self::T_AT_VARIABLES_START);
					}
				/*
				 * @variables media types
				 */
				elseif ($c == "{" && $currentState == self::T_AT_VARIABLES_START)
					{
					$buffer 	= strtolower(trim($buffer, $sDefaultTrim . "{"));
					$r[]		= array(self::T_AT_VARIABLES_START, $scope);
					$scope		= $buffer != "" ? array_filter(array_map("trim", explode(",", $buffer))) : $sDefaultScope;
					$i			= $i++;
					$buffer		= "";
					array_pop($state);
					array_push($state, self::T_AT_VARIABLES);
					}
				/*
				 * @variables declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_VARIABLES)
					{
					$property	= trim($buffer, $sDefaultTrim . ":");
					$buffer		= "";
					array_push($state, self::T_VARIABLE_DECLARATION);
					}
				/*
				 * @variables declaration: Value
				 */
				elseif (($c == ";" || $c == "}") && $currentState == self::T_VARIABLE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_VARIABLE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @variable declaration closed with a right curly brace => closes @variables block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_VARIABLES_END);
						$scope	= $sDefaultScope;
						}
					}
				/*
				 * Parse error: @variable declaration value 
				 */
				elseif ($c == ":" && $currentState == self::T_VARIABLE_DECLARATION)
					{
					$errorStart	= strrpos($css, "\n", -($l - strrpos($css, ":", -($l - $i + 2))));
					$errorEnd	= strpos($css, "\n", $errorStart + 1);
					$errorLine	= trim(substr($css, $errorStart, $errorEnd - $errorStart));
					trigger_error("Line #" . $line . ": Unterminated @variable declaration:\n<code>" . $errorLine . "_</code>", E_USER_WARNING);
					}
				/*
				 * End of at-rule @variables block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_VARIABLES)
					{
					$r[]		= array(self::T_AT_VARIABLES_END);
					$scope		= $sDefaultScope;
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @import
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 7)) == "@import")
					{
					$i			= $i + 7;
					$buffer 	= "";
					array_push($state, self::T_AT_IMPORT);
					}
				/*
				 * End of at-rule @import
				 */
				elseif (($c == ";" || $c == "\n") && $currentState == self::T_AT_IMPORT)
					{
					$scopes = array_filter(array_map("trim", explode(",", strtolower(trim($buffer, $sDefaultTrim . ";")))));
					if (stripos($importUrl, "url(") !== false)
						{
						$importUrl = rtrim(substr($importUrl, 4), $sDefaultTrim . ")");
						}
					$importUrl	= trim($importUrl, $sDefaultTrim . "\"'");
					$r[]		= array(self::T_AT_IMPORT, $importUrl, $scopes);
					$buffer 	= "";
					array_pop($state);
					}
				/*
				 * Start of document level at-rule
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT)
					{
					$buffer		= "";
					array_push($state, self::T_AT_RULE);
					}
				/*
				 * End of document level at-rule
				 */
				elseif ($c == ";" && $currentState == self::T_AT_RULE)
					{
					$pos		= strpos($buffer, " ");
					$rule		= substr($buffer, 0, $pos);
					$value		= trim(substr($buffer, $pos), $sDefaultTrim . ";");
					$r[]		= array(self::T_AT_RULE, $rule, $value);
					$buffer		= "";
					array_pop($state);
					}
				/**
				 * Selector
				 */
				elseif ($c == "," && ($currentState == self::T_AT_MEDIA || $currentState ==  self::T_DOCUMENT))
					{
					$selectors[]= trim($buffer, $sDefaultTrim . ",");
					$buffer		= "";
					}
				/*
				 * Start of ruleset
				 */
				elseif ($c == "{" && ($currentState == self::T_AT_MEDIA || $currentState == self::T_DOCUMENT))
					{
					$selectors[]= trim($buffer, $sDefaultTrim . "{");
					$selectors 	= array_filter(array_map("trim", $selectors));
					$r[]		= array(self::T_RULESET_START);
					$r[]		= array(self::T_SELECTORS, $selectors);
					$r[]		= array(self::T_DECLARATIONS_START);
					$buffer		= "";
					$selectors	= array();
					array_push($state, self::T_DECLARATIONS);
					}
				/*
				 * Declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_DECLARATIONS)
					{
					$property	= trim($buffer, $sDefaultTrim . ":;");
					$buffer		= "";
					array_push($state, self::T_DECLARATION);
					}
				/*
				 * Declaration: Value
				 */
				elseif (($c == ";" || $c == "}") && $currentState == self::T_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // declaration closed with a right curly brace => close ruleset
						{
						array_pop($state);
						$r[]	= array(self::T_DECLARATIONS_END);
						$r[]	= array(self::T_RULESET_END);
						}
					}
				/*
				 * Parse error: declaration value
				 */
				elseif ($c == ":" && $currentState == self::T_DECLARATION)
					{
					// Fix for Internet Explorer declaration filter as the declaration value conrains a colon (Ex.: progid:DXImageTransform.Microsoft.Alpha(Opacity=85);)
					if (strpos(strtolower($property), "filter") !== false && strtolower(trim($buffer)) == "progid:")
						{
						continue;
						}
					$errorStart	= strrpos($css, "\n", -($l - strrpos($css, ":", -($l - $i + 2))));
					$errorEnd	= strpos($css, "\n", $errorStart + 1);
					$errorLine	= trim(substr($css, $errorStart, $errorEnd - $errorStart));
					trigger_error("Line #" . $line . ": Unterminated declaration:\n<code>" . $errorLine . "_</code>", E_USER_WARNING);
					}
				/*
				 * End of ruleset
				 */
				elseif ($c == "}" && $currentState == self::T_DECLARATIONS)
					{
					$r[]		= array(self::T_DECLARATIONS_END);
					$r[]		= array(self::T_RULESET_END);
					$buffer		= "";
					array_pop($state);
					}
				}
			$p = $c; // Set the parent char
			}
		return $r;
		}
	/**
	 * Transforms "opacity: {value}" into browser specific counterparts.
	 * 
	 * @param string $property Property
	 * @param string $value Value
	 * @return string
	 */
	private static function _opacityTransformation($property, $value)
		{
		// Calculate the value for Internet Explorer filter statement
		$ieValue = (int) ((float) $value * 100);
		$r  = "-moz-opacity:" . $value . ";";						// Firefox < 3.5
		$r .= "-ms-filter: \"alpha(opacity=" . $ieValue . ")\";";	// Internet Explorer 8
		$r .= "filter: alpha(opacity=" . $ieValue . ");zoom:1;";	// Internet Explorer 4 - 7
		return $r;
		}
	/**
	 * Transforms "white-space: pre-wrap" into browser specific counterparts.
	 * 
	 * @param string $property Property
	 * @param string $value Value
	 * @return string
	 */
	private static function _whiteSpacePreWrapTransformation($property, $value)
		{
		if (strtolower($value) == "pre-wrap")
			{
			$r  = "white-space:-moz-pre-wrap;";		// Mozilla
			$r .= "white-space:-webkit-pre-wrap;";	// Webkit
			$r .= "white-space:-pre-wrap;";			// Opera 4 - 6
			$r .= "white-space:-o-pre-wrap;";		// Opera 7+
			$r .= "word-wrap:break-word;";			// Internet Explorer 5.5+
			return $r;
			}
		else
			{
			return "";
			}
		}
	/**
	 * Convert a HSL value to hexadecimal notation.
	 * 
	 * Based on: {@link http://www.easyrgb.com/index.php?X=MATH&H=19#text19}.
	 * 
	 * @param integer $hue Hue
	 * @param integer $saturation Saturation
	 * @param integer $lightness Lightnesss
	 * @return string
	 */
	private static function _hsl2hex($hue, $saturation, $lightness)
		{
		$hue		= $hue / 360;
		$saturation	= $saturation / 100;
		$lightness	= $lightness / 100;
		if ($saturation == 0)
			{
			$red	= $lightness * 255;
			$green	= $lightness * 255;
			$blue	= $lightness * 255;
			}
		else
			{
			if ($lightness < 0.5 )
				{
				$v2 = $lightness * (1 + $saturation);
				}
			else
				{
				$v2 = ($lightness + $saturation) - ($saturation * $lightness);
				}
			$v1		= 2 * $lightness - $v2;
			$red	= 255 * self::_hue2rgb($v1, $v2, $hue + (1 / 3));
			$green	= 255 * self::_hue2rgb($v1, $v2, $hue);
			$blue	= 255 * self::_hue2rgb($v1, $v2, $hue - (1 / 3));
			}
		return "#" . str_pad(dechex(round($red)), 2, "0", STR_PAD_LEFT) . str_pad(dechex(round($green)), 2, "0", STR_PAD_LEFT) . str_pad(dechex(round($blue)), 2, "0", STR_PAD_LEFT);
		}
	/**
	 * Apply hue to a rgb color value.
	 * 
	 * @param integer $v1 Value 1
	 * @param integer $v2 Value 2
	 * @param integer $hue Hue
	 * @return integer
	 */
	private static function _hue2rgb($v1, $v2, $hue)
		{
		if ($hue < 0)
			{
			$hue += 1;
			}
		if ($hue > 1)
			{
			$hue -= 1;
			}
		if ((6 * $hue) < 1)
			{
			return ($v1 + ($v2 - $v1) * 6 * $hue);
			}
		if ((2 * $hue) < 1)
			{
			return ($v2);
			}
		if ((3 * $hue) < 2)
			{
			return ($v1 + ($v2 - $v1) * (( 2 / 3) - $hue) * 6);
			}
		return $v1;
		}
	}
