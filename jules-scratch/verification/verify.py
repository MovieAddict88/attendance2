import re
from playwright.sync_api import sync_playwright, expect

def run_verification(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Navigate to the main page
    # Assuming a web server is running and serving the project root at localhost
    try:
        page.goto("http://localhost:8000/index.php", timeout=5000)
    except Exception as e:
        print(f"Could not connect to http://localhost:8000/index.php. Aborting.")
        print(f"Error: {e}")
        browser.close()
        return


    # 1. Screenshot of the main landing page
    page.screenshot(path="jules-scratch/verification/01_main_page.png")
    expect(page.get_by_role("heading", name="Welcome to the School Data Management System")).to_be_visible()

    # 2. Navigate to Admin Portal and screenshot login
    page.get_by_role("link", name="Admin Portal").click()
    expect(page).to_have_url(re.compile(r".*admin/"))
    expect(page.get_by_role("heading", name="Admin Login")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/02_admin_login.png")

    # 3. Log in as admin
    page.get_by_label("Username").fill("admin")
    page.get_by_label("Password").fill("password")
    page.get_by_role("button", name="Login").click()

    # 4. Screenshot of the Admin Dashboard
    expect(page).to_have_url(re.compile(r".*admin/dashboard.php"))
    expect(page.get_by_role("heading", name="Welcome, admin!")).to_be_visible()
    expect(page.get_by_text("Total Students")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/03_admin_dashboard.png")

    # 5. Navigate to Manage Teachers
    page.get_by_role("link", name="Manage Teachers").click()
    expect(page).to_have_url(re.compile(r".*manage_teachers.php"))
    expect(page.get_by_role("heading", name="Manage Teachers")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/04_manage_teachers.png")

    print("Verification script ran successfully.")
    browser.close()

with sync_playwright() as playwright:
    run_verification(playwright)