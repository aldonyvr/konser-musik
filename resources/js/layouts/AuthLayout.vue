<template>
	<!--begin::Authentication Layout -->
	<div class="d-flex justify-content-center "
		:style="`background-image: url('${setting?.bg_auth}'); background-size: cover; `">
		<!--begin::Body-->
		<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
			<!--begin::Card-->
			<div class="bg-light d-flex flex-column align-items-stretch flex-center rounded-4 w-md-550px p-16 w-150">
				<!--begin::Wrapper-->
				<div class="d-flex flex-center flex-column flex-column-fluid ">
					<router-view></router-view>
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Card-->
		</div>
		<!--end::Body-->
	</div>
	<!--end::Authentication Layout -->
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, onMounted } from "vue";
import LayoutService from "@/core/services/LayoutService";
import { useBodyStore } from "@/stores/body";
import { useSetting } from "@/services";

export default defineComponent({
	name: "auth-layout",
	components: {},
	setup() {
		const store = useBodyStore();
		const { data: setting = {} } = useSetting()

		onMounted(() => {
			LayoutService.emptyElementClassesAndAttributes(document.body);

			store.addBodyClassname("app-blank");
			store.addBodyClassname("bg-body");
		});

		return {
			getAssetPath,
			setting
		};
	},
});
</script>
