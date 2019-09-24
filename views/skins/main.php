<div
	:class="[ 'cbw-skin-' + action ]"
>
	<div
		class="cbw-body__title"
		v-html="pageTitle"
	></div>
	<p><?php
		_e( 'Each landing comes with the custom demo content and a predefined set of plugins. Depending upon the selected landing the wizard will install the required plugins and some demo data.', 'croco-ik' );
	?></p>
	<cx-vui-tabs
		:invert="true"
		:in-panel="true"
		:value="firstTab"
	>
		<cx-vui-tabs-panel
			v-for="( typeLabel, typeSlug ) in allowedTypes"
			:name="typeSlug"
			:label="typeLabel"
			:key="typeSlug"
			v-if="'select' === action"
		>
			<div class="cbw-skins-list">
				<cbw-skin
					v-for="( skin, slug ) in skinsByTypes[ typeSlug ]"
					:skin="skin"
					:slug="slug"
					:key="typeSlug + slug"
				></cbw-skin>
			</div>
		</cx-vui-tabs-panel>
	</cx-vui-tabs>
	<cx-vui-button
		tag-name="a"
		:url="backURL"
	>
		<svg slot="label" width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.67089 0L-4.76837e-07 6L5.67089 12L7 10.5938L2.65823 6L7 1.40625L5.67089 0Z" fill="#007CBA"/></svg>
		<span slot="label"><?php _e( 'Back', 'croco-ik' ); ?></span>
	</cx-vui-button>
</div>