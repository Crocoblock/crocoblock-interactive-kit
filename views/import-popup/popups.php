<div class="cbw-import-popup">
	<div
		class="cbw-body__title"
		v-html="pageTitle"
	></div>
	<div class="cbw-popups-filters">
		<div
			class="cbw-popups-filters__item"
			v-for="filter in filters"
			:key="filter.slug"
		>
			<cx-vui-select
				:label="filter.name"
				:options-list="filter.options"
				:wrapper-css="['vertical-layout']"
				v-model="activeFilters[ filter.slug ]"
			></cx-vui-select>
		</div>
	</div>
	<div class="cbw-popups-list">
		<cbw-popup
			v-for="( popup, slug ) in filteredPopups"
			@start-popup-import="startImport( $event )"
			:key="slug"
			:popup="popup"
			:slug="popup.slug"
		></cbw-popup>
	</div>
	<cx-vui-popup
		v-model="importing"
		ok-label="<?php _e( 'Go to popup', 'jet-engine' ) ?>"
		cancel-label="<?php _e( 'Close', 'jet-engine' ) ?>"
		:show-ok="importData.status"
		:show-cancel="importData.status"
		@on-cancel="handleCancel"
		@on-ok="goToPopup"
		body-width="400px"
	>
		<div class="cx-vui-subtitle" slot="title"><?php
			_e( 'Import popup', 'jet-engine' );
		?></div>
		<div class="cbw-import-progress" slot="content">
			<div class="cbw-import-progress__status">{{ this.importData.statusString }}</div>
		</div>
	</cx-vui-popup>
</div>