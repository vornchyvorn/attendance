import matplotlib.pyplot as plt
import matplotlib.patches as patches

def draw_table(ax, x, y, title, fields, color='#F0F0F0'):
    ax.add_patch(patches.Rectangle((x, y - len(fields)*0.4), 4, len(fields)*0.4 + 0.6,
                                   linewidth=1, edgecolor='black', facecolor=color))
    ax.text(x + 2, y + 0.2, title, ha='center', va='center', fontsize=10, fontweight='bold')
    for i, field in enumerate(fields):
        ax.text(x + 0.2, y - 0.4*i - 0.4, field, ha='left', va='center', fontsize=8)

fig, ax = plt.subplots(figsize=(16, 10))
ax.set_xlim(0, 20)
ax.set_ylim(-15, 5)
ax.axis('off')

tables = {
    "users": {"pos": (1, 0), "fields": ["id", "student_id", "username", "password", "gender", 
                                        "Education_level", "school_year", "gmail", "major", 
                                        "date", "user_type", "phone", "address", "image"]},
    "attendance": {"pos": (7, 0), "fields": ["id", "event_id", "student_id", "username", 
                                             "major", "check_in", "check_out", "time_period"]},
    "events": {"pos": (13, 2), "fields": ["id", "title", "description", "event_date", 
                                          "event_start", "event_end", "location", "created_at"]},
    "majors": {"pos": (1, -10), "fields": ["id", "major_name"]},
    "admin": {"pos": (7, -10), "fields": ["id", "admin_id", "username", "password", "role", 
                                          "email", "phone", "image"]},
    "announcements": {"pos": (13, -10), "fields": ["id", "title", "content", "created_at", 
                                                   "created_by", "status"]}
}

for table_name, table_info in tables.items():
    draw_table(ax, *table_info["pos"], table_name, table_info["fields"])

def draw_arrow(start, end, text=""):
    ax.annotate("",
                xy=end, xycoords='data',
                xytext=start, textcoords='data',
                arrowprops=dict(arrowstyle="->", lw=1.5))
    if text:
        mid_x = (start[0] + end[0]) / 2
        mid_y = (start[1] + end[1]) / 2
        ax.text(mid_x, mid_y + 0.5, text, ha='center', fontsize=8)

draw_arrow((5, 0), (7, 0), "users.student_id → attendance.student_id")
draw_arrow((11, 0), (13, 2), "attendance.event_id → events.id")
draw_arrow((1, -0.4*8), (1, -10 + 0.4), "users.major → majors.major_name")
draw_arrow((11, -10 + 0.4), (13, -10 + 0.4), "announcements.created_by → admin.username")

plt.tight_layout()
plt.savefig("attendance_system_ERD.png", dpi=300)
plt.savefig("attendance_system_ERD.pdf")
plt.show()
