import subprocess
import time
import os
from playwright.sync_api import sync_playwright, expect

# --- 1. Start the PHP server ---
port = 8008
server_process = subprocess.Popen(
    ['php', '-S', f'localhost:{port}'],
    stdout=subprocess.DEVNULL,
    stderr=subprocess.DEVNULL
)
print(f"PHP server started on http://localhost:{port}")
time.sleep(2)

# --- 2. Run Playwright verification ---
try:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        base_url = f"http://localhost:{port}"

        # --- Step 1: Complete the setup process ---
        print("Navigating to setup page...")
        page.goto(f"{base_url}/setup.php")

        page.get_by_role("link", name="Start Setup").click()
        expect(page).to_have_url(f"{base_url}/setup.php?step=database")
        print("On database setup page.")

        page.get_by_label("Database Host").fill("localhost")
        page.get_by_label("Database Name").fill("sms_db_test")
        page.get_by_label("Database Username").fill("root")
        page.get_by_label("Database Password").fill("password")
        page.get_by_role("button", name="Create Database & Tables").click()

        # --- DEBUGGING: Check for success or failure ---
        try:
            # Wait for the "Installation Complete" heading
            expect(page.get_by_role("heading", name="Installation Complete")).to_be_visible(timeout=10000)
            print("Setup completed successfully.")
        except Exception as e:
            print("\n--- VERIFICATION FAILED ---")
            print("Error: Did not find 'Installation Complete' heading.")
            # Check for a database error message on the page
            error_locator = page.locator(".alert-danger")
            if page.locator(".alert-danger").is_visible():
                print(f"Found a specific error message on the page: '{error_locator.inner_text()}'\n")
            else:
                print("No specific error message was found on the page.\n")
            print("--- Page Content Dump ---")
            print(page.content())
            print("--- End Page Content Dump ---\n")
            raise e

        # --- Step 2: Log in as Admin ---
        page.get_by_role("link", name="Go to Your Application").click()
        expect(page.get_by_role("heading", name="Welcome Back!")).to_be_visible()
        print("On login page.")

        page.get_by_label("Username").fill("admin")
        page.get_by_label("Password").fill("Admin@123")
        page.get_by_role("button", name="Login").click()

        # --- Step 3: Verify Admin Dashboard and take screenshot ---
        print("Verifying admin dashboard...")
        expect(page).to_have_url(f"{base_url}/admin", timeout=10000)
        expect(page.get_by_role("heading", name="Admin Dashboard")).to_be_visible()
        print("Successfully logged in and redirected to Admin Dashboard.")

        screenshot_path = "jules-scratch/verification/admin_dashboard.png"
        page.screenshot(path=screenshot_path)
        print(f"Screenshot saved to {screenshot_path}")

        # --- Step 4: Verify navigation ---
        print("Verifying navigation to teacher management...")
        page.get_by_role('link', name='Manage Teachers').click()
        expect(page.get_by_role("heading", name="Manage Teachers")).to_be_visible()
        print("Successfully navigated to Manage Teachers.")

        print("Verifying navigation to student management...")
        # Navigate back to dashboard first to click the sidebar link again
        page.get_by_role('link', name='Dashboard').click()
        expect(page.get_by_role("heading", name="Admin Dashboard")).to_be_visible()
        page.get_by_role('link', name='Manage Students').click()
        expect(page.get_by_role("heading", name="Manage Students")).to_be_visible()
        print("Successfully navigated to Manage Students.")

        browser.close()

finally:
    # --- 3. Stop the server ---
    print("Stopping PHP server...")
    server_process.terminate()
    try:
        server_process.wait(timeout=5)
    except subprocess.TimeoutExpired:
        server_process.kill()
    print("Server stopped.")