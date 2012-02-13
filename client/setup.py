from cx_Freeze import setup, Executable

setup(
        name = "Laptop Tracker Client",
        version = "0.2",
        description = "Client code for the FOSS laptop tracking system",
        executables = [Executable("tracker.py")])
