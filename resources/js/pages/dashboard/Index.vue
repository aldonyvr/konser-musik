<script setup lang="ts">

</script>

<template>
  <main>
    <div class="card mb-10">
      <!--begin::Heading-->
      <div
        class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-200px bg-warning">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column text-white pt-10">
          <span class="fw-bold fs-2x">D A S H B O A R D</span>
        </h3>
        <div class="d-flex justify-content-end mt-10">
          <!-- <select2 placeholder="Pilih Tahun" class="form-select-solid mw-200px mw-md-100" name="tahun" :options="tahuns"
            v-model="tahun">
          </select2> -->
        </div>
        <!--end::Title-->
      </div>
      <!--end::Heading-->

      <!--begin::Body-->
      <div class="card-body mt-n20">
        <!--begin::Stats-->
        <div class="mt-n20 position-relative">
          <!--begin::Row-->
          <div class="row g-3 g-lg-6">
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-profile-user fs-2x text-info">
                      <i class="path1"></i>
                      <i class="path2"></i>
                      <i class="path3"></i>
                      <i class="path4"></i>
                    </i>
                  </span>
                </div>
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Total Konser</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-abstract-46 fs-2x text-success">
                      <i class="path1"></i>
                      <i class="path2"></i>
                    </i>
                  </span>
                </div>
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Total Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-dollar fs-2x text-success">
                      <i class="path1"></i>
                      <i class="path2"></i>
                      <i class="path3"></i>
                    </i>
                  </span>
                </div>
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Jumlah Saldo Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <div class="col-md-3">
              <!--begin::Items-->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-profile-user fs-2x text-info">
                      <i class="path1"></i>
                      <i class="path2"></i>
                      <i class="path3"></i>
                      <i class="path4"></i>
                    </i>
                  </span>
                </div>
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Data Pengguna</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>

            </div>  
            </div>
            </div>  
          </div>
  </main>
</template>
