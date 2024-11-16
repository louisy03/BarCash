from tkinter import Tk
from views.login_view import LoginView

def main():
    # Configuración inicial de la ventana principal
    root = Tk()
    root.title("BarCash")
    root.geometry("400x300")
    root.resizable(False, False)

    # Cargar la vista de inicio de sesión
    LoginView(root)
    root.mainloop()

if __name__ == "__main__":
    main()
