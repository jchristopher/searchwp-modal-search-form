const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const {
  ServerSideRender,
  PanelBody,
  ExternalLink,
  SelectControl,
  TextControl
} = wp.components;
const { withSelect } = wp.data;
const { InspectorControls } = wp.editor;
const { Fragment } = wp.element;

const icon = "search";

registerBlockType("searchwp/modal-form", {
  title: __("Modal Form", "searchwpmodalform"),
  description: (
    <Fragment>
      <p>{__("Insert a modal search form", "searchwpmodalform")}</p>
      <ExternalLink
        className={_SEARCHWP_MODAL_FORM_DATA.searchwp ? "hidden" : ""}
        href="https://searchwp.com/?utm_source=wordpressorg&amp;utm_medium=link&amp;utm_campaign=modalform&amp;utm_content=menuitem"
      >
        {__("Improve your search with SearchWP", "searchwpmodalform")}
      </ExternalLink>
    </Fragment>
  ),

  icon,
  category: "searchwp",

  keywords: [
    __("modal", "searchwpmodalform"),
    __("search", "searchwpmodalform"),
    __("overlay", "searchwpmodalform")
  ],

  attributes: {
    engine: {
      type: "string",
      default: "default"
    },
    template: {
      type: "string",
      default: "Default"
    },
    text: {
      type: "string",
      default: __("Search", "searchwpmodalform")
    },
    type: {
      type: "string",
      default: "link"
    }
  },

  edit: withSelect(function(select) {
    return {
      post_id: select("core/editor").getCurrentPostId()
    };
  })(function({ post_id, setAttributes, attributes, isSelected }) {
    const { engine, template, text, type } = attributes;
    return (
      <div>
        <InspectorControls>
          <PanelBody initialOpen={true}>
            <TextControl
              label={__("Text", "searchwpmodalform")}
              value={text}
              onChange={value => {
                setAttributes({ text: value });
              }}
            />

            <SelectControl
              label={__("Template", "searchwpmodalform")}
              value={`${template}`}
              options={_SEARCHWP_MODAL_FORM_DATA.templates}
              onChange={value => {
                setAttributes({ template: value });
              }}
            />

            <SelectControl
              label={__("Type", "searchwpmodalform")}
              value={`${type}`}
              options={[
                {
                  label: __("Link", "searchwpmodalform"),
                  value: "link"
                },
                {
                  label: __("Button", "searchwpmodalform"),
                  value: "button"
                }
              ]}
              onChange={value => {
                setAttributes({ type: value });
              }}
            />
          </PanelBody>
        </InspectorControls>

        <ServerSideRender
          block="searchwp/modal-form"
          attributes={{ engine, template, text, type }}
        />
      </div>
    );
  }),

  save: function() {
    return null;
  }
});
