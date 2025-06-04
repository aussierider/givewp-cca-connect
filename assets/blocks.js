
/**
 * Gutenberg Blocks for CCAvenue Gateway
 */

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl } = wp.components;
    
    registerBlockType('givewp-ccavenue/donation-info', {
        title: 'CCAvenue Donation Info',
        icon: 'money-alt',
        category: 'widgets',
        
        attributes: {
            showPAN: {
                type: 'boolean',
                default: true,
            },
            showAddress: {
                type: 'boolean',
                default: true,
            },
            title: {
                type: 'string',
                default: 'Tax Exemption Information',
            },
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { showPAN, showAddress, title } = attributes;
            
            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Settings' },
                        el(TextControl, {
                            label: 'Title',
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: 'Show PAN Information',
                            checked: showPAN,
                            onChange: function(value) {
                                setAttributes({ showPAN: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: 'Show Address Information',
                            checked: showAddress,
                            onChange: function(value) {
                                setAttributes({ showAddress: value });
                            }
                        })
                    )
                ),
                
                el('div', { className: 'givewp-ccavenue-block-preview' },
                    el('h3', {}, title),
                    el('p', {}, 'CCAvenue donation information block preview'),
                    showPAN && el('div', { className: 'preview-item' }, '📄 PAN Number Benefits'),
                    showAddress && el('div', { className: 'preview-item' }, '🏠 Address Information'),
                    el('div', { className: 'preview-item' }, '🔒 Secure Payment Processing')
                )
            ];
        },
        
        save: function() {
            return null; // Rendered server-side
        },
    });
    
})(window.wp);
