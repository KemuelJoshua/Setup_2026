export type AppNotification = {
    id: string;
    title: string;
    message: string;
    createdAt: string;
    isUnread: boolean;
};

export type NotificationSummary = {
    items: AppNotification[];
    unreadCount: number;
};
