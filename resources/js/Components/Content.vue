<script setup></script>
<template>
    <main class="app-main">
        <!-- Content Header (Page header) -->
        <div class="app-content-header" v-if="$slots.header">
            <div class="container-fluid">
                <slot name="header"></slot>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="app-content" id="contentAdmin">
            <div class="container-fluid">
                <slot />
            </div>
        </div>
        <!-- /.content -->
    </main>
</template>
