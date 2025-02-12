<script setup lang="ts">
import QrScanner from '@/components/QrScanner.vue';
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Mitra</h3>
                    </div>
                    <div class="card-body">
                        <QrScanner />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
