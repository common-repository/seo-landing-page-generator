(function( $ ) {
	'use strict';

	tinymce.PluginManager.add('issslpg_tinymce_shortcode_button', function(editor, url) {

		var tinymce_menu = [];


		//
		// CTA Button
		//

		if ( issslpg_active_shortcodes.is_cta_button_usage_allowed ) {
			tinymce_menu.push({
				text: 'CTA Button',
				onclick: function () {
					editor.windowManager.open({
						title: 'Insert CTA Button Shortcode',
						body: [
							{
								type: 'listbox',
								name: 'style',
								label: 'Style',
								values: [
									{text: 'Default', value: ''},
									{text: 'Outline', value: 'outline'},
									{text: 'No Style', value: 'no-style'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'type',
								label: 'Type',
								values: [
									{text: 'Default', value: ''},
									{text: 'Phone', value: 'phone'},
									{text: 'Email', value: 'email'},
								],
								value: ''
							},
							{
								type: 'textbox',
								name: 'prefix',
								label: 'Prefix',
								value: ''
							},
							{
								type: 'textbox',
								name: 'title',
								label: 'Title',
								value: ''
							},
							{
								type: 'textbox',
								name: 'suffix',
								label: 'Suffix',
								value: ''
							},
							{
								type: 'textbox',
								name: 'href',
								label: 'URL',
								value: ''
							},
							{
								type: 'listbox',
								name: 'target',
								label: 'Link Target',
								values: [
									{text: 'Open Link in Same Tab', value: ''},
									{
										text: 'Open Link in New Tab',
										value: '_blank'
									},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'font_style',
								label: 'Font Style',
								values: [
									{text: 'Default', value: ''},
									{text: 'Bold', value: 'bold'},
									{text: 'Bold Italic', value: 'bold-italic'},
									{text: 'Italic', value: 'italic'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'size',
								label: 'Size',
								values: [
									{text: 'Default', value: ''},
									{text: 'Large', value: 'large'},
									{text: 'Extra Large', value: 'extra-large'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'rounded',
								label: 'Rounded',
								values: [
									{text: 'No', value: ''},
									{text: 'Yes', value: 'rounded'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'shadow',
								label: 'Shadow',
								values: [
									{text: 'None', value: ''},
									{text: 'Small', value: 'small'},
									{text: 'Medium', value: 'medium'},
									{text: 'Large', value: 'large'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'icon',
								label: 'Icon',
								values: [
									{text: 'None', value: ''},
									{text: 'Phone', value: 'phone-1'},
									{text: 'Phone 2', value: 'phone-2'},
									{text: 'Email', value: 'email-1'},
									{text: 'Facebook', value: 'facebook'},
									{text: 'Instagram', value: 'instagram'},
									{text: 'LinkedIn', value: 'linkedin'},
									{text: 'Pinterest', value: 'pinterest'},
									{text: 'Tiktok', value: 'tiktok'},
									{text: 'Twitter', value: 'twitter'},
									{text: 'YouTube', value: 'youtube'},
								],
								value: ''
							},
							{
								type: 'listbox',
								name: 'width',
								label: 'Width',
								values: [
									{text: 'Auto', value: ''},
									{text: 'Full', value: 'full'},
								],
								value: ''
							},
							{
								type: 'textbox',
								name: 'text_color',
								label: 'Text Color',
								value: ''
							},
							{
								type: 'textbox',
								name: 'bg_color',
								label: 'BG Color',
								value: ''
							},
							{
								type: 'textbox',
								name: 'hover_bg_color',
								label: 'Hover BG Color',
								value: ''
							},
						],
						onsubmit: function (e) {
							const type = e.data.type ? ` type="${e.data.type}"` : '';
							const href = e.data.href ? ` href="${e.data.href}"` : '';
							const target = e.data.target ? ` target="${e.data.target}"` : '';
							const style = e.data.style ? ` style="${e.data.style}"` : '';
							const text_color = e.data.text_color ? ` text_color="${e.data.text_color}"` : '';
							const bg_color = e.data.bg_color ? ` bg_color="${e.data.bg_color}"` : '';
							const hover_bg_color = e.data.hover_bg_color ? ` hover_bg_color="${e.data.hover_bg_color}"` : '';
							const size = e.data.size ? ` size="${e.data.size}"` : '';
							const font_style = e.data.font_style ? ` font_style="${e.data.font_style}"` : '';
							const width = e.data.width ? ` width="${e.data.width}"` : '';
							const rounded = e.data.rounded ? ` rounded="${e.data.rounded}"` : '';
							const shadow = e.data.shadow ? ` shadow="${e.data.shadow}"` : '';
							const icon = e.data.icon ? ` icon="${e.data.icon}"` : '';
							const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
							const title = e.data.title ? ` title="${e.data.title}"` : '';
							const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
							editor.insertContent(
								`[iss_cta_button${type}${href}${target}${style}${text_color}${bg_color}${hover_bg_color}${icon}${size}${font_style}${width}${rounded}${shadow}${prefix}${title}${suffix}]`,
							);
						}
					});
				}
			}); // END CTA Button
		}

		//
		// Large Market Content
		//

		if ( issslpg_active_shortcodes.local_static_content && !issslpg_active_shortcodes.on_template_page ) {
			tinymce_menu.push( {
				text: 'Large Market Content',
				onclick: function() {
					editor.insertContent(
						'[iss_large_market_content]'
					);
				}
			} );
		}

		//
		// Alt. Large Market Content
		//

		if ( issslpg_active_shortcodes.alt_large_market_content && !issslpg_active_shortcodes.on_template_page ) {
			tinymce_menu.push( {
				text: 'Alternative Large Market Content',
				onclick: function() {
					editor.insertContent(
						'[iss_alternative_large_market_content]'
					);
				}
			} );
		}

		//
		// Local Static Content
		//

		if ( issslpg_active_shortcodes.local_static_content && !issslpg_active_shortcodes.on_template_page ) {
			tinymce_menu.push( {
				text: 'Local Static Content',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert Local Static Content Shortcode',
						body: [
							{
								type:  'listbox',
								name:  'block',
								label: 'Block',
								values: [
									{ text: 'Local Static Content Block 1',  value: '1'  },
									{ text: 'Local Static Content Block 2',  value: '2'  },
									{ text: 'Local Static Content Block 3',  value: '3'  },
									{ text: 'Local Static Content Block 4',  value: '4'  },
									{ text: 'Local Static Content Block 5',  value: '5'  },
									{ text: 'Local Static Content Block 6',  value: '6'  },
									{ text: 'Local Static Content Block 7',  value: '7'  },
									{ text: 'Local Static Content Block 8',  value: '8'  },
									{ text: 'Local Static Content Block 9',  value: '9'  },
									{ text: 'Local Static Content Block 10', value: '10' },
								]
							}
						],
						onsubmit: function( e ) {
							editor.insertContent(
								'[iss_local_static_content block="' + e.data.block + '"]'
							);
						}
					});
				},
			} );
		}

		//
		// Local Image
		//

		if ( issslpg_active_shortcodes.local_images && !issslpg_active_shortcodes.on_template_page ) {
			tinymce_menu.push( {
				text: 'Local Image',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert Local Image Shortcode',
						body: [
							{
								type:  'listbox',
								name:  'size',
								label: 'Size',
								'values': [
									{ text: 'Medium', value: 'medium' },
									{ text: 'Large',  value: 'large'  },
									{ text: 'Full',   value: 'full'   }
								],
								value: 'large'
							},
							{
								type:  'listbox',
								name:  'alignment',
								label: 'Alignment',
								'values': [
									{ text: 'None',   value: ''   },
									{ text: 'Left',   value: 'left'   },
									{ text: 'Center', value: 'center' },
									{ text: 'Right',  value: 'right'  }
								]
							},
							{
								type:  'textbox',
								name:  'classes',
								label: 'Class',
								value: ''
							}
						],
						onsubmit: function( e ) {
							const size = e.data.size ? ` size="${e.data.size}"` : '';
							const classes = e.data.classes ? ` class="${e.data.classes}"` : '';
							const alignment = e.data.alignment ? ` alignment="${e.data.alignment}"` : '';
							editor.insertContent(
								`[iss_local_image${size}${classes}${alignment}]`,
							);
						}
					} );
				}
			} );
		}


		/* FAQ
		 ------------------------------------------------------------ */
		if ( issslpg_active_shortcodes.is_faq_usage_allowed ) {
			tinymce_menu.push( {
				text: 'FAQ',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert FAQ Shortcode',
						body: [
							{
								type:  'listbox',
								name:  'accordion',
								label: 'Accordion',
								values: [
									{ text: 'On', value: 'on' },
									{ text: 'Off',  value: ''  },
								],
								value: 'on'
							}
						],
						onsubmit: function( e ) {
							const accordion = e.data.accordion ? 'faq_accordion' : 'faq';
							editor.insertContent(
								`[iss_${accordion}]`,
							);
						}
					} );
				}
			} );
		}


		/* Local Image Slider
		 ------------------------------------------------------------ */
		if ( issslpg_active_shortcodes.local_images && !issslpg_active_shortcodes.on_template_page ) {
			tinymce_menu.push( {
				text: 'Local Image Slider',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert Local Image Slider Shortcode',
						body: [
							{
								type:  'listbox',
								name:  'size',
								label: 'Size',
								'values': [
									{ text: 'Medium', value: 'medium' },
									{ text: 'Large',  value: 'large'  },
									{ text: 'Full',   value: 'full'   }
								],
								value: 'large'
							},
							{
								type:  'listbox',
								name:  'autoplay',
								label: 'Autoplay',
								'values': [
									{ text: 'On',  value: 'on'   },
									{ text: 'Off', value: ''     }
								],
								value: 'true'
							},
						],
						onsubmit: function( e ) {
							const size = e.data.size ? ` size="${e.data.size}"` : '';
							const autoplay = e.data.autoplay ? ` auto="${e.data.autoplay}"` : '';
							editor.insertContent(
								`[iss_local_image_slider${size}${autoplay}]`,
							);
						}
					} );
				}
			} );
		}

		//
		// Demographics
		//

		if ( issslpg_active_shortcodes.is_demographics_usage_allowed ) {

			const demographics_shortcodes = [
				{
					menu_title: 'Geo ID',
					title: 'Insert Geo ID Shortcode',
					shortcode: 'iss_geo_id',
				},
				{
					menu_title: 'City Type',
					title: 'Insert City Type Shortcode',
					shortcode: 'iss_city_type',
				},
				{
					menu_title: 'Population',
					title: 'Insert Population Shortcode',
					shortcode: 'iss_population',
				},
				{
					menu_title: 'Households',
					title: 'Insert Households Shortcode',
					shortcode: 'iss_households',
				},
				{
					menu_title: 'Median Income',
					title: 'Insert Median Income Shortcode',
					shortcode: 'iss_median_income',
				},
				{
					menu_title: 'Land Area',
					title: 'Insert Land Area Shortcode',
					shortcode: 'iss_land_area',
				},
				{
					menu_title: 'Water Area',
					title: 'Insert Water Area Shortcode',
					shortcode: 'iss_water_area',
				},
				{
					menu_title: 'Latitude',
					title: 'Insert Latitude Shortcode',
					shortcode: 'iss_latitude',
				},
				{
					menu_title: 'Longitude',
					title: 'Insert Longitude Shortcode',
					shortcode: 'iss_longitude',
				},
			];

			demographics_shortcodes.forEach( function(item, index) {
				tinymce_menu.push( {
					text: item.menu_title,
					onclick: function() {
						editor.windowManager.open({
							title: item.title,
							body: [
								{
									type: 'textbox',
									name: 'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type: 'textbox',
									name: 'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function (e) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[${item.shortcode}${prefix}${suffix}]`,
								);
							}
						});
					}
				} );
			});
		}

		//
		// Dynamic Shortcodes
		//

		if ( issslpg_shortcode_data.length !== 0 && !issslpg_active_shortcodes.on_template_page ) {
			const dynamic_shortcode_data = JSON.parse(issslpg_shortcode_data);

			$.each(dynamic_shortcode_data, function( shortcode_type_name, shortcode_type ) {
				$.each(shortcode_type, function( shortcode_tag, shortcode_data ) {

					switch(shortcode_type_name) {

						// Image
						case 'image':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'listbox',
												name:  'size',
												label: 'Size',
												values: [
													{ text: 'Medium', value: 'medium' },
													{ text: 'Large',  value: 'large'  },
													{ text: 'Full',   value: 'full'   },
												],
												value: 'large'
											},
											{
												type:  'listbox',
												name:  'alignment',
												label: 'Alignment',
												values: [
													{ text: 'None',   value: ''       },
													{ text: 'Left',   value: 'left'   },
													{ text: 'Center', value: 'center' },
													{ text: 'Right',  value: 'right'  },
												]
											},
											{
												type:  'textbox',
												name:  'classes',
												label: 'Class',
												value: '',
											}
										],
										onsubmit: function( e ) {
											const size = e.data.size ? ` size="${e.data.size}"` : '';
											const classes = e.data.classes ? ` class="${e.data.classes}"` : '';
											const alignment = e.data.alignment ? ` alignment="${e.data.alignment}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${size}${classes}${alignment}]`,
											);
										}
									} );
								}
							} );
							break;

						// Image Slider
						case 'image_slider':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'listbox',
												name:  'size',
												label: 'Size',
												values: [
													{ text: 'Medium', value: 'medium' },
													{ text: 'Large',  value: 'large'  },
													{ text: 'Full',   value: 'full'   }
												],
												value: 'large'
											},
											{
												type:  'listbox',
												name:  'autoplay',
												label: 'Autoplay',
												values: [
													{ text: 'On',  value: 'on'   },
													{ text: 'Off', value: ''     }
												],
												value: 'true'
											},
										],
										onsubmit: function( e ) {
											const size = e.data.size ? ` size="${e.data.size}"` : '';
											const autoplay = e.data.autoplay ? ` auto="${e.data.autoplay}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${size}${autoplay}]`,
											);
										}
									} );
								}
							} );
							break;

						// Default
						default:
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.insertContent(
										'[' + shortcode_data.tag + ']'
									);
								}
							} );
							break;
					  }

				});
			});
		}

		//
		// Add button to toolbar with more menu items
		//

		editor.addButton('issslpg_tinymce_shortcode_button', {
			title: 'SEO Landing Page Generator Shortcodes',
			type: 'menubutton',
			icon: 'icon issslpg-tinymce-shortcode-button-icon',
			menu: [


				/* Site Name
				 ------------------------------------------------------------ */
				{
					text: 'Site Name',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Site Name Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_site_name${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Site Name


				/* Page Title
				 ------------------------------------------------------------ */
				{
					text: 'Page Title',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Page Title Shortcode',
							body: [
								{
									type:  'listbox',
									name:  'lowercase',
									label: 'Lowercase',
									values: [
										{ text: 'On', value: 'on' },
										{ text: 'Off',  value: ''  },
									],
									value: 'on'
								},
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const lowercase = e.data.lowercase ? ` lowercase="${e.data.lowercase}"` : '';
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_page_title${prefix}${suffix}${lowercase}]`,
								);
							}
						});
					}
				}, // END Page Title


				/* City
				 ------------------------------------------------------------ */
				{
					text: 'City',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_city${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City


				/* State
				 ------------------------------------------------------------ */
				{
					text: 'State',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert State Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_state${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END State


				/* Province
				 ------------------------------------------------------------ */
				{
					text: 'Province',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Province Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_province${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Province


				/* Territory
				 ------------------------------------------------------------ */
				{
					text: 'Territory',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Territory Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_territory${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Territory


				/* Abbr. State
				 ------------------------------------------------------------ */
				{
					text: 'Abbr. State',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Abbr. State Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_state_abbr${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Abbr. State


				/* Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Zip Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Zip Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_zip_code${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Zip Code


				/* Postcode
				 ------------------------------------------------------------ */
				{
					text: 'Postcode',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Postcode Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_postcode${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Postcode


				/* Postal Code
				 ------------------------------------------------------------ */
				{
					text: 'Postal Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Postal Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_postal_code${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Postal Code


				/* Zip Codes
				 ------------------------------------------------------------ */
				{
					text: 'Zip Codes',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Zip Codes Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_zip_codes${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Zip Codes


				/* Postcodes
				 ------------------------------------------------------------ */
				{
					text: 'Postcodes',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Postcodes Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_postcodes${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Postcodes


				/* Postal Codes
				 ------------------------------------------------------------ */
				{
					text: 'Postal Codes',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Postal Codes Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_postal_codes${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Postal Codes


				/* County
				 ------------------------------------------------------------ */
				{
					text: 'County',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert County Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_county${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END County


				/* Counties
				 ------------------------------------------------------------ */
				{
					text: 'Counties',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Counties Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_counties${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Counties


				/* Phone Number
				 ------------------------------------------------------------ */
				{
					text: 'Phone Number',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Phone Number Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								editor.insertContent(
									`[iss_phone_number${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Phone Number


				/* Phone Link
				 ------------------------------------------------------------ */
				{
					text: 'Phone Link',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Phone Link Shortcode',
							body: [
								{
									type:  'listbox',
									name:  'font_size',
									label: 'Size',
									values: [
										{ text: 'Default',  value: ''  },
										{ text: 'Medium', value: 'medium' },
										{ text: 'Large', value: 'large' },
										{ text: 'Extra Large', value: 'extra-large' },
									],
									value: ''
								},
								{
									type:  'listbox',
									name:  'font_weight',
									label: 'Font Weight',
									values: [
										{ text: 'Default',  value: ''  },
										{ text: 'Medium', value: 'medium' },
										{ text: 'Semibold', value: 'semibold' },
										{ text: 'Bold', value: 'bold' },
									],
									value: 'bold'
								},
								{
									type:  'listbox',
									name:  'font_style',
									label: 'Font Style',
									values: [
										{ text: 'Default',  value: ''  },
										{ text: 'Italic', value: 'italic' },
									],
									value: ''
								},
								{
									type:  'listbox',
									name:  'text_decoration',
									label: 'Text Decoration',
									values: [
										{ text: 'Default',  value: ''  },
										{ text: 'No Underline / No Underline', value: 'no-underline-no-underline' },
										{ text: 'Underline / Underline', value: 'underline-underline' },
										{ text: 'No Underline / Underline', value: 'no-underline-underline' },
										{ text: 'Underline / No Underline ', value: 'underline-no-underline' },
									],
									value: ''
								},
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'title',
									label: 'Title',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'listbox',
									name:  'target',
									label: 'Link Target',
									values: [
										{ text: 'Open Link in Same Tab',  value: ''  },
										{ text: 'Open Link in New Tab', value: '_blank' },
									],
									value: ''
								},
								{
									type:  'textbox',
									name:  'link_color',
									label: 'Color',
									value: ''
								},
								{
									type:  'textbox',
									name:  'link_hover_color',
									label: 'Hover Color',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const font_size        = e.data.font_size ? ` font_size="${e.data.font_size}"` : '';
								const font_weight      = e.data.font_weight ? ` font_weight="${e.data.font_weight}"` : '';
								const font_style       = e.data.font_style ? ` font_style="${e.data.font_style}"` : '';
								const text_decoration  = e.data.text_decoration ? ` text_decoration="${e.data.text_decoration}"` : '';
								const prefix           = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const title            = e.data.title ? ` title="${e.data.title}"` : '';
								const suffix           = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const target           = e.data.target ? ` target="${e.data.target}"` : '';
								const link_color       = e.data.link_color ? ` link_color="${e.data.link_color}"` : '';
								const link_hover_color = e.data.link_hover_color ? ` link_hover_color="${e.data.link_hover_color}"` : '';
								editor.insertContent(
									`[iss_phone_link${font_size}${font_weight}${font_style}${text_decoration}${prefix}${title}${suffix}${target}${link_color}${link_hover_color}]`,
								);
							}
						});
					}
				}, // END Phone Link


				/* City, County
				 ------------------------------------------------------------ */
				{
					text: 'City, County',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, County Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_county${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, County


				/* City, State
				 ------------------------------------------------------------ */
				{
					text: 'City, State',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, State Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_state${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, State


				/* City, State, Zip Codes
				 ------------------------------------------------------------ */
				{
					text: 'City, State, Zip Codes',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, State, Zip Codes Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_state_zip_code${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, State, Zip Codes


				/* City, Abbr. State
				 ------------------------------------------------------------ */
				{
					text: 'City, Abbr. State',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, Abbr. State Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_state_abbr${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, Abbr. State


				/* Cities in County
				 ------------------------------------------------------------ */
				{
					text: 'Cities in County',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Cities in County Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_cities_in_county${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Cities in County


				/* Site Name, City, State, Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Site Name, City, State, Zip Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Site Name, City, State, Zip Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_site_name_city_state_zip_code${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Site Name, City, State, Zip Code


				/* Site Name, City, Abbr. State, Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Site Name, City, Abbr. State, Zip Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Site Name, City, Abbr. State, Zip Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_site_name_city_state_abbr_zip_code${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Site Name, City, Abbr. State, Zip Code


				/* Page Title, City, State, Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Page Title, City, State, Zip Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Page Title, City, State, Zip Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_page_title_city_state_zip_code${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Page Title, City, State, Zip Code


				/* Page Title, City, Abbr. State, Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Page Title, City, Abbr. State, Zip Code',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Page Title, City, Abbr. State, Zip Code Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_page_title_city_state_abbr_zip_code${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END Page Title, City, Abbr. State, Zip Code


				/* City, State, Zip Code, Phone Number
				 ------------------------------------------------------------ */
				{
					text: 'City, State, Zip Code, Phone Number',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, State, Zip Code, Phone Number Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_state_zip_code_phone_number${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, State, Zip Code, Phone Number


				/* City, Abbr. State, Zip Code, Phone Number
				 ------------------------------------------------------------ */
				{
					text: 'City, Abbr. State, Zip Code, Phone Number',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert City, Abbr. State, Zip Code, Phone Number Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'prefix',
									label: 'Prefix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'suffix',
									label: 'Suffix',
									value: ''
								},
								{
									type:  'textbox',
									name:  'join',
									label: 'Join',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const prefix = e.data.prefix ? ` prefix="${e.data.prefix}"` : '';
								const suffix = e.data.suffix ? ` suffix="${e.data.suffix}"` : '';
								const join = e.data.join ? ` join="${e.data.join}"` : '';
								editor.insertContent(
									`[iss_city_state_abbr_zip_code_phone_number${join}${prefix}${suffix}]`,
								);
							}
						});
					}
				}, // END City, Abbr. State, Zip Code, Phone Number


				/* Random Location Format
				 ------------------------------------------------------------ */
				{
					text: 'Random Location Format',
					onclick: function() {
						editor.insertContent(
							'[iss_random_location_format]'
						);
					}
				}, // END Random Location Format


				/* Local Office Address
				 ------------------------------------------------------------ */
				{
					text: 'Local Office Address',
					onclick: function() {
						editor.insertContent(
							'[iss_local_office_address]'
						);
					}
				}, // END Local Office Address


				/* Local Office Street
				 ------------------------------------------------------------ */
				{
					text: 'Local Office Street',
					onclick: function() {
						editor.insertContent(
							'[iss_local_office_street]'
						);
					}
				}, // END Local Office Street


				/* Local Office City
				 ------------------------------------------------------------ */
				{
					text: 'Local Office City',
					onclick: function() {
						editor.insertContent(
							'[iss_local_office_city]'
						);
					}
				}, // END Local Office City


				/* Local Office Zip Code
				 ------------------------------------------------------------ */
				{
					text: 'Local Office Zip Code',
					onclick: function() {
						editor.insertContent(
							'[iss_local_office_zip_code]'
						);
					}
				}, // END Local Office Zip Code


				/* Related Landing Pages
				 ------------------------------------------------------------ */
				{
					text: 'Related Landing Pages',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Related Landing Pages Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'title',
									label: 'Title',
									value: ''
								},
								{
									type:  'textbox',
									name:  'limit',
									label: 'Limit',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const title = e.data.title ? ` title="${e.data.title}"` : '';
								const limit = e.data.limit ? ` limit="${e.data.limit}"` : '';
								editor.insertContent(
									`[iss_related_landing_pages${title}${limit}]`,
								);
							}
						});
					}
				}, // END Related Landing Pages


				/* Map
				 ------------------------------------------------------------ */
				{
					text: 'Map',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Map Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'class',
									label: 'Class',
									value: ''
								},
								{
									type:  'textbox',
									name:  'width',
									label: 'Width',
									value: ''
								},
								{
									type:  'textbox',
									name:  'height',
									label: 'Height',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const classes = e.data.class ? ` title="${e.data.class}"` : '';
								const width = e.data.width ? ` width="${e.data.width}"` : '';
								const height = e.data.height ? ` height="${e.data.height}"` : '';
								editor.insertContent(
									`[iss_map${classes}${width}${height}]`,
								);
							}
						});
					}
				}, // END Map


				/* Directions Map
				 ------------------------------------------------------------ */
				{
					text: 'Directions Map',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Insert Directions Map Shortcode',
							body: [
								{
									type:  'textbox',
									name:  'class',
									label: 'Class',
									value: ''
								},
								{
									type:  'textbox',
									name:  'width',
									label: 'Width',
									value: ''
								},
								{
									type:  'textbox',
									name:  'height',
									label: 'Height',
									value: ''
								},
							],
							onsubmit: function( e ) {
								const classes = e.data.class ? ` title="${e.data.class}"` : '';
								const width = e.data.width ? ` width="${e.data.width}"` : '';
								const height = e.data.height ? ` height="${e.data.height}"` : '';
								editor.insertContent(
									`[iss_directions_map${classes}${width}${height}]`,
								);
							}
						});
					}
				}, // END Directions Map


				/* Sitemap
				 ------------------------------------------------------------ */
				{
					text: 'Sitemap',
					onclick: function() {
						editor.insertContent(
							'[iss_sitemap]'
						);
					}
				}, // END Sitemap


			].concat(tinymce_menu)
		});
	});
})( jQuery );