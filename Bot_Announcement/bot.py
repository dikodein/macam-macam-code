from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import Application, CommandHandler, MessageHandler, filters, ContextTypes
import json
import os

# Ganti dengan token bot Anda
TOKEN = "Token_Bot_anda"
OWNER_USERNAME = "username_owner"
ADMIN_USERNAME = "username_admin"  # Admin yang akan dihubungi
USERS_FILE = "users.json"

def load_users():
    if os.path.exists(USERS_FILE):
        with open(USERS_FILE, "r") as file:
            return json.load(file)
    return []

def save_users(users):
    with open(USERS_FILE, "w") as file:
        json.dump(users, file)

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user = update.effective_user
    users = load_users()
    if user.id not in users:
        users.append(user.id)
        save_users(users)
    await update.message.reply_text("Selamat datang di bot announcement! Jika Anda butuh bantuan, cukup kirim pesan.")

async def announce(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    user = update.effective_user
    if user.username != OWNER_USERNAME:
        await update.message.reply_text("Anda bukan owner bot ini!")
        return
    
    if not context.args:
        await update.message.reply_text("Gunakan /ann <pesan> untuk mengirim pengumuman.")
        return
    
    message = " ".join(context.args)
    users = load_users()
    for user_id in users:
        try:
            await context.bot.send_message(chat_id=user_id, text=f"📢 Pengumuman: {message}")
        except Exception as e:
            print(f"Gagal mengirim pesan ke {user_id}: {e}")
    
    await update.message.reply_text("Pengumuman telah dikirim!")

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Menanggapi semua pesan dari pengguna dengan tombol Hubungi Admin."""
    user_message = update.message.text.lower()

    # Tampilkan tombol "Hubungi Admin"
    keyboard = [[InlineKeyboardButton("Hubungi Admin", url=f"https://t.me/{ADMIN_USERNAME}")]]
    reply_markup = InlineKeyboardMarkup(keyboard)

    response_text = (
        "Kami telah menerima pesan Anda. Jika ini mendesak, silakan hubungi admin kami dengan tombol di bawah. "
        "Admin akan membantu Anda secepat mungkin. 😊"
    )

    await update.message.reply_text(response_text, reply_markup=reply_markup)

def main():
    app = Application.builder().token(TOKEN).build()
    
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("ann", announce))
    app.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))

    print("Bot sedang berjalan...")
    app.run_polling()

if __name__ == "__main__":
    main()
