import type { VariantProps } from "class-variance-authority"
import { cva } from "class-variance-authority"

export { default as Button } from "./Button.vue"

export const buttonVariants = cva(
  [
    "inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap",
    "rounded-lg text-sm font-medium",
    "transition-[color,background-color,border-color,box-shadow,transform]",
    "duration-200 ease-out",
    "outline-none",
    "disabled:pointer-events-none disabled:opacity-50",
    "active:scale-[0.98]",
    "focus-visible:border-ring",
    "focus-visible:ring-2 focus-visible:ring-ring/40",
    "focus-visible:ring-offset-2 focus-visible:ring-offset-background",
    "aria-invalid:border-destructive",
    "aria-invalid:ring-2 aria-invalid:ring-destructive/20",
    "dark:aria-invalid:ring-destructive/40",
    "[&_svg]:pointer-events-none",
    "[&_svg]:shrink-0",
    "[&_svg:not([class*='size-'])]:size-4",
  ],
  {
    variants: {
      variant: {
        default: [
          "bg-primary text-primary-foreground",
          "shadow-sm shadow-primary/15",
          "hover:bg-primary/90 hover:shadow-md hover:shadow-primary/20",
          "active:bg-primary/85 active:shadow-sm",
        ],

        destructive: [
          "bg-destructive text-white",
          "shadow-sm shadow-destructive/15",
          "hover:bg-destructive/90",
          "hover:shadow-md hover:shadow-destructive/20",
          "focus-visible:ring-destructive/30",
          "active:bg-destructive/85",
          "dark:bg-destructive/80",
          "dark:hover:bg-destructive/90",
        ],

        outline: [
          "border border-border",
          "bg-background text-foreground",
          "shadow-xs",
          "hover:border-primary/30",
          "hover:bg-accent hover:text-accent-foreground",
          "active:bg-accent/80",
          "dark:border-input",
          "dark:bg-input/20",
          "dark:hover:bg-input/40",
        ],

        secondary: [
          "bg-secondary text-secondary-foreground",
          "shadow-xs",
          "hover:bg-secondary/80 hover:shadow-sm",
          "active:bg-secondary/70",
        ],

        ghost: [
          "text-foreground",
          "hover:bg-accent hover:text-accent-foreground",
          "active:bg-accent/70",
          "dark:hover:bg-accent/50",
        ],

        link: [
          "h-auto rounded-sm p-0",
          "text-primary shadow-none",
          "underline-offset-4",
          "hover:underline",
          "active:scale-100",
          "focus-visible:ring-offset-4",
        ],
      },

      size: {
        default: [
          "h-10 px-4 py-2",
          "has-[>svg]:px-3.5",
        ],

        sm: [
          "h-8 rounded-md px-3",
          "text-xs gap-1.5",
          "has-[>svg]:px-2.5",
        ],

        lg: [
          "h-11 px-6",
          "text-sm gap-2.5",
          "has-[>svg]:px-4.5",
        ],

        icon: "size-10 p-0",
        "icon-sm": "size-8 rounded-md p-0",
        "icon-lg": "size-11 p-0",
      },
    },

    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
)

export type ButtonVariants = VariantProps<typeof buttonVariants>