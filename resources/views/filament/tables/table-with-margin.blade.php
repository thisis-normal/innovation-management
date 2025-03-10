@php
    $actions = $getActions();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $columns = $getColumns();
    $content = $getContent();
    $description = $getDescription();
    $heading = $getHeading();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $isLoaded = $isLoaded();
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $isSelectionEnabled = $isSelectionEnabled();
    $isStriped = $isStriped();
    $recordAction = $getRecordAction();
    $recordUrl = $getRecordUrl();
    $records = $getRecords();
    $shouldMountContent = $shouldMountContent();
@endphp

<div class="mb-8">
    <x-filament-tables::table
        :actions="$actions"
        :actions-position="$actionsPosition"
        :actions-column-label="$actionsColumnLabel"
        :columns="$columns"
        :content="$content"
        :description="$description"
        :heading="$heading"
        :header="$header"
        :header-actions="$headerActions"
        :is-loaded="$isLoaded"
        :is-reorderable="$isReorderable"
        :is-reordering="$isReordering"
        :is-selection-enabled="$isSelectionEnabled"
        :is-striped="$isStriped"
        :record-action="$recordAction"
        :record-url="$recordUrl"
        :records="$records"
        :should-mount-content="$shouldMountContent"
    />
</div>
