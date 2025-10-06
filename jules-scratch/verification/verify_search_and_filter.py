from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()

    # Navigate to the home page
    page.goto("http://localhost:8080/index.php")

    # Take a screenshot of the initial page
    page.screenshot(path="jules-scratch/verification/initial_view.png")

    # Fill out the search form and apply a filter
    page.get_by_placeholder("Search by car name...").fill("Toyota")
    page.get_by_role("combobox").select_option("Available")
    page.get_by_role("button", name="Filter").click()

    # Wait for navigation and take a screenshot of the filtered results
    page.wait_for_load_state("networkidle")
    page.screenshot(path="jules-scratch/verification/filtered_view.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)