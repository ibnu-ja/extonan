<script lang="ts">
	import { RangeCalendar as RangeCalendarPrimitive } from "bits-ui";
	import { cn, type WithoutChildrenOrChild } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		value,
		onchange,
		...restProps
	}: WithoutChildrenOrChild<RangeCalendarPrimitive.YearSelectProps> = $props();
</script>

<span
	class={cn(
		"has-focus:border-ring border-input has-focus:ring-ring/50 relative flex rounded-[var(--radius-sm)] border shadow-xs has-focus:ring-[3px]",
		className
	)}
>
	<RangeCalendarPrimitive.YearSelect bind:ref class="absolute inset-0 opacity-0" {...restProps}>
		{#snippet child({ props, yearItems, selectedYearItem })}
			<select {...props} {value} {onchange}>
				{#each yearItems as yearItem (yearItem.value)}
					<option
						value={yearItem.value}
						selected={value !== undefined
							? yearItem.value === value
							: yearItem.value === selectedYearItem.value}
					>
						{yearItem.label}
					</option>
				{/each}
			</select>
			<span
				class="[&>svg]:text-muted-foreground flex h-(--cell-size) items-center gap-1 rounded-[var(--radius-sm)] ps-2 pe-1 text-sm font-medium select-none [&>svg]:size-3.5"
				aria-hidden="true"
			>
				{yearItems.find((item) => item.value === value)?.label || selectedYearItem.label}
				<Icon
					lucide="ChevronDownIcon"
					tabler="IconChevronDown"
					hugeicons="ArrowDownIcon"
					phosphor="CaretDownIcon"
					remixicon="RiArrowDownSLine"
					class={cn("size-4", className)}
				/>
			</span>
		{/snippet}
	</RangeCalendarPrimitive.YearSelect>
</span>
